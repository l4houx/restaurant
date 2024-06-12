<?php

namespace App\DataFixtures;

use App\Entity\Company\Client;
use App\Entity\User\SalesPerson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppClientFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    public function load(ObjectManager $manager): void
    {
        /** @var array<SalesPerson> $salesPersons */
        $salesPersons = $manager->getRepository(SalesPerson::class)->findAll();

        foreach ($salesPersons as $salesPerson) {
            for ($index = 1; $index <= 20; ++$index) {
                $client = (new Client())
                    ->setMember($salesPerson->getMember())
                    ->setSalesPerson($salesPerson)
                ;

                $client->getAddress()
                    ->setLocality('Paris')
                    ->setZipCode('75000')
                    ->setEmail('email@email.com')
                    ->setPhone('0123456789')
                    ->setStreetAddress('1 rue de la mairie')
                ;

                $client
                    ->setName($this->faker()->unique()->name())
                    ->setCompanyNumber('21931232314430')
                ;

                $manager->persist($client);
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
            AppSalesPersonFixtures::class,
        ];
    }
}
