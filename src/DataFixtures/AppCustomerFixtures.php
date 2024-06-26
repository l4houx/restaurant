<?php

namespace App\DataFixtures;

use App\Entity\User\Customer;
use App\Entity\Company\Client;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppCustomerFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly AvatarService $avatarService,
        private readonly SluggerInterface $slugger
    ) {
        $this->autoIncrement = 16;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<Client> $clients */
        $clients = $manager->getRepository(Client::class)->findAll();

        foreach ($clients as $client) {
            $manager->persist($this->createUser()->setClient($client));
        }

        $manager->flush();
    }

    private function createUser(): Customer
    {
        $genres = ['male', 'female'];
        $genre = $this->faker()->randomElement($genres);

        /** @var Customer $user */
        //$avatar = $this->avatarService->createAvatar($user->getEmail());
        $user = (new Customer())
            //->setAvatar($avatar)
            ->setIsVerified(true)
            ->setIsAgreeTerms(true)
            ->setRoles([HasRoles::CUSTOMER])
            ->setLastName($this->faker()->lastName)
            ->setFirstName($this->faker()->firstName($genre))
            ->setUsername(sprintf('customer+%d', $this->autoIncrement))
            ->setEmail(sprintf('customer+%d@yourdomain.com', $this->autoIncrement))
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $user->setPassword($this->hasher->hashPassword($user, 'customer'));

        ++$this->autoIncrement;

        return $user;
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppClientFixtures::class,
        ];
    }
}
