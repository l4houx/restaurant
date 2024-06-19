<?php

namespace App\DataFixtures;

use App\Entity\Company\Member;
use App\Entity\Data\Purchase;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppDataFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        /** @var array<Member> $members */
        $members = $manager->getRepository(Member::class)->findAll();

        foreach ($members as $member) {
            $purchase = (new Purchase())
                ->setMode(Purchase::MODE_BANK_WIRE)
                ->setAccount($member->getAccount())
                ->setPoints(5000)
                ->setState('accepted')
                ->prepare()
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $purchase->getWallet()->addTransaction($purchase);

            $manager->persist($purchase);
        }

        /** @var array<User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $purchase = (new Purchase())
                ->setMode(Purchase::MODE_BANK_WIRE)
                ->setAccount($user->getAccount())
                ->setPoints(7000)
                ->setState('accepted')
                ->prepare()
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $purchase->getWallet()->addTransaction($purchase);

            $manager->persist($purchase);
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [AppCustomerFixtures::class];
    }
}
