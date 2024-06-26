<?php

namespace App\DataFixtures;

use App\Entity\Company\Member;
use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use App\Entity\User\Collaborator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $manager->persist($this->createUser()->setMember($member)->setPhone($this->faker()->phoneNumber));
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
            ->setIsVerified(true)
            ->setIsAgreeTerms(true)
            ->setRoles([HasRoles::COLLABORATOR])
            ->setLastName($this->faker()->lastName)
            ->setFirstName($this->faker()->firstName($genre))
            ->setUsername(sprintf('collaborator+%d', $this->autoIncrement))
            ->setEmail(sprintf('collaborator+%d@yourdomain.com', $this->autoIncrement))
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $user->setPassword($this->hasher->hashPassword($user, 'collaborator'));

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
