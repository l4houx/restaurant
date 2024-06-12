<?php

namespace App\DataFixtures;

use App\Entity\Company\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppOrganizationFixtures extends Fixture
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        $organization = (new Organization())
            ->setName($this->faker()->company)
            ->setCompanyNumber('21931232314430')
        ;

        $manager->persist($organization);
        $this->addReference('organization', $organization);

        $manager->flush();
    }
}
