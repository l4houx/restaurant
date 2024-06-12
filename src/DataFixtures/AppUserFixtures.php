<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\AvatarService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppUserFixtures extends Fixture implements DependentFixtureInterface
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
        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            //$manager->persist($this->createUser($user));

            $genres = ['male', 'female'];
            $genre = $this->faker()->randomElement($genres);

            /** @var User $user */
            //$avatar = $this->avatarService->createAvatar($user->getEmail());
            $user = (new User())
                //->setAvatar($avatar)
                ->setLastName($this->faker()->lastName)
                ->setFirstName($this->faker()->firstName($genre))
                ->setUsername(sprintf('user+%d', $this->autoIncrement))
                ->setEmail(sprintf('user+%d@email.com', $this->autoIncrement))
                ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setLastLoginIp($this->faker()->ipv4())
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $user->setPassword($this->hasher->hashPassword($user, 'user'));

            if ($this->autoIncrement > 5) {
                $user->setIsVerified(false);
                $user->setIsSuspended($this->faker()->numberBetween(0, 1));
                $user->setIsAgreeTerms(false);
                $user->setDeletedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')));
            } else {
                $user->setIsVerified(true);
                $user->setIsAgreeTerms(true);
            }

            ++$this->autoIncrement;
            $this->addReference('user-'.$this->autoIncrement, $user);

            $manager->flush();
        }
    }

    private function createUser(): User
    {
        $genres = ['male', 'female'];
        $genre = $this->faker()->randomElement($genres);

        /** @var User $user */
        //$avatar = $this->avatarService->createAvatar($user->getEmail());
        $user = (new User())
            //->setAvatar($avatar)
            ->setLastName($this->faker()->lastName)
            ->setFirstName($this->faker()->firstName($genre))
            ->setUsername(sprintf('user+%d', $this->autoIncrement))
            ->setEmail(sprintf('user+%d@email.com', $this->autoIncrement))
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $user->setPassword($this->hasher->hashPassword($user, 'user'));

        if ($this->autoIncrement > 5) {
            $user->setIsVerified(false);
            $user->setIsSuspended($this->faker()->numberBetween(0, 1));
            $user->setIsAgreeTerms(false);
            $user->setDeletedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')));
        } else {
            $user->setIsVerified(true);
            $user->setIsAgreeTerms(true);
        }

        ++$this->autoIncrement;
        $this->addReference('user-'.$this->autoIncrement, $user);

        return $user;
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppCustomerFixtures::class,
        ];
    }
}
