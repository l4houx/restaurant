<?php

namespace App\DataFixtures;

use App\Service\AvatarService;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SuperAdministrator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppAdministratorTeamFixtures extends Fixture
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly AvatarService $avatarService,
        private readonly SluggerInterface $slugger
    ) {
        $this->autoIncrement = 1;
    }

    public function load(ObjectManager $manager): void
    {
        // SuperAdministrator Super Admin Application
        /** @var SuperAdministrator $superadmin */
        $superadmin = (new SuperAdministrator());
        $superadmin
            ->setId(1)
            // ->setAvatar($this->avatarService->createAvatar($superadmin->getEmail()))
            // ->setTeamName('superadmin.jpg')
            ->setRoles([HasRoles::ADMINAPPLICATION])
            ->setLastname('Cameron')
            ->setFirstname('Williamson')
            ->setUsername('superadmin')
            // ->setSlug('superadmin')
            ->setEmail('superadmin@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Super Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $this->setReference('SuperAdministrator', $superadmin);

        $manager->persist(
            $superadmin->setPassword(
                $this->hasher->hashPassword($superadmin, 'superadmin')
            )
        );

        // SuperAdministrator Admin
        /** @var SuperAdministrator $admin */
        $admin = (new SuperAdministrator());
        $admin
            ->setId(2)
            // ->setAvatar($this->avatarService->createAvatar($admin->getEmail()))
            // ->setTeamName('admin.jpg')
            ->setRoles([HasRoles::ADMIN])
            ->setLastname('Wade')
            ->setFirstname('Warren')
            ->setUsername('admin')
            // ->setSlug('admin')
            ->setEmail('admin@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Admin Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $manager->persist(
            $admin->setPassword(
                $this->hasher->hashPassword($admin, 'admin')
            )
        );

        // SuperAdministrator Moderator
        /** @var SuperAdministrator $moderator */
        $moderator = (new SuperAdministrator());
        $moderator
            ->setId(3)
            // ->setAvatar($this->avatarService->createAvatar($moderator->getEmail()))
            // ->setTeamName('moderator.jpg')
            ->setRoles([HasRoles::MODERATOR])
            ->setLastname('Jane')
            ->setFirstname('Cooper')
            ->setUsername('moderator')
            // ->setSlug('moderator')
            ->setEmail('moderator@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Moderator Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $manager->persist(
            $moderator->setPassword(
                $this->hasher->hashPassword($moderator, 'moderator')
            )
        );

        // SuperAdministrator Editor
        /** @var SuperAdministrator $editor */
        $editor = (new SuperAdministrator());
        $editor
            ->setId(4)
            // ->setAvatar($this->avatarService->createAvatar($editor->getEmail()))
            // ->setTeamName('editor.jpg')
            ->setRoles([HasRoles::EDITOR])
            ->setLastname('Roberto')
            ->setFirstname('Cooper')
            ->setUsername('editor')
            // ->setSlug('editor')
            ->setEmail('editor@yourdomain.com')
            // ->setPhone($this->faker()->phoneNumber())
            ->setIsTeam(true)
            ->setIsAgreeTerms(true)
            ->setIsVerified(true)
            ->setAbout($this->faker()->realText(254))
            ->setDesignation('Editor Staff')
            ->setLastLogin(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setLastLoginIp($this->faker()->ipv4())
            ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
        ;

        $manager->persist(
            $editor->setPassword(
                $this->hasher->hashPassword($editor, 'editor')
            )
        );

        $manager->flush();
    }
}
