<?php

namespace App\DataFixtures;

use App\Entity\Company\Member;
use App\Entity\Company\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppMemberFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        /** @var Organization $organization */
        $organization = $this->getReference('organization');

        for ($i = 1; $i <= 5; ++$i) {
            /** @var Member $member */
            $member = (new Member())
                ->setOrganization($organization)
                ->setName($this->faker()->unique()->name)
                ->setCompanyNumber('21931232314430')
            ;

            $member->getAddress()
                ->setLocality($this->faker()->city)
                ->setZipCode($this->faker()->postcode)
                ->setEmail($this->faker()->email)
                ->setPhone($this->faker()->phoneNumber)
                ->setStreetAddress($this->faker()->address)
            ;

            $manager->persist($member);
            $this->addReference(sprintf('member_%d', $i), $member);
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppOrganizationFixtures::class,
        ];
    }
}
