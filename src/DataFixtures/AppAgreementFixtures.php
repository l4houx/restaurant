<?php

namespace App\DataFixtures;

use App\Entity\Rules;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppAgreementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Rules $rules */
        $rules = $this->getReference('rules');

        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if ($user->getId() % 3 > 0) {
                $user->acceptRules($rules);
            }
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
            AppRulesFixtures::class,
        ];
    }
}
