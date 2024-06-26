<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $genres = ['male', 'female'];
            $genre = $this->faker()->randomElement($genres);

            /** @var User $user */
            //$avatar = $this->avatarService->createAvatar($user->getEmail());
            $user = (new User())
                //->setAvatar($avatar)
                ->setIsVerified(true)
                ->setIsAgreeTerms(true)
                ->setRoles([HasRoles::DEFAULT])
                ->setLastName($this->faker()->lastName)
                ->setFirstName($this->faker()->firstName($genre))
                ->setUsername(sprintf('user+%d', $this->autoIncrement))
                ->setEmail(sprintf('user+%d@yourdomain.com', $this->autoIncrement))
                ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setLastLoginIp($this->faker()->ipv4())
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $user->setPassword($this->hasher->hashPassword($user, 'user'));

            ++$this->autoIncrement;
            $this->addReference('user-'.$this->autoIncrement, $user);

            $manager->flush();
        }
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
