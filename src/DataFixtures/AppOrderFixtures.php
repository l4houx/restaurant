<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Order\Line;
use App\Entity\Order\Order;
use App\Entity\Shop\Product;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\User\SalesPerson;
use App\Entity\User\Collaborator;
use App\Entity\User\SuperAdministrator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Workflow\WorkflowInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AppOrderFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly WorkflowInterface $orderStateMachine
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        /** @var Product $product */
        $product = $manager->getRepository(Product::class)->findOneByAmount(2000);

        /**
         * @var int  $k
         * @var User $user
         */
        foreach ($users as $k => $user) {
            if ($user instanceof Customer) {
                $address = $user->getClient()->getMember()?->getAddress();
            } else {
                /** @var SalesPerson|Collaborator|Manager|SuperAdministrator $user */
                $address = $user->getMember()?->getAddress();
            }

            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);

            $order = (new Order())
                ->setAddress($address)
                ->setUser($user)
            ;

            $order->getLines()->add((new Line())->increaseQuantity()->setOrder($order)->setProduct($product));

            $manager->persist($order);

            $this->orderStateMachine->apply($order, 'valid_cart');
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppCustomerFixtures::class,
            AppShopFixtures::class,
        ];
    }
}
