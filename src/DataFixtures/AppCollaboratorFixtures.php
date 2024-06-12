<?php

namespace App\DataFixtures;

use App\Entity\Company\Member;
use App\Entity\User\Collaborator;
use App\Service\AvatarService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppCollaboratorFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly AvatarService $avatarService,
        private readonly SluggerInterface $slugger
    ) {
        $this->autoIncrement = 11;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<Member> $members */
        $members = $manager->getRepository(Member::class)->findAll();

        foreach ($members as $member) {
            $manager->persist($this->createUser()->setMember($member)->setPhone('0123456789'));
        }

        $manager->flush();
    }

    private function createUser(): Collaborator
    {
        $genres = ['male', 'female'];
        $genre = $this->faker()->randomElement($genres);

        /** @var Collaborator $user */
        //$avatar = $this->avatarService->createAvatar($user->getEmail());
        $user = (new Collaborator())
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

        return $user;
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppSalesPersonFixtures::class,
        ];
    }
}
