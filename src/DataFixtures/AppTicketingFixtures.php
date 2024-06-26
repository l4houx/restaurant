<?php

namespace App\DataFixtures;

use App\Entity\Tickets\Level;
use App\Entity\Tickets\Response;
use App\Entity\Tickets\Status;
use App\Entity\Tickets\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppTicketingFixtures extends Fixture implements DependentFixtureInterface
{
    use FakerTrait;

    private int $autoIncrement;

    public function __construct()
    {
        $this->autoIncrement = 1;
    }

    public function load(ObjectManager $manager): void
    {
        /** @var array<int, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        // Create Level
        $urgencyLevels = [];
        $urgencyLevel = new Level();
        $urgencyLevel->setName('Urgent')->setColor('#eb2f06');
        $manager->persist($urgencyLevel);
        $urgencyLevels[] = $urgencyLevel;

        $mediumLevels = [];
        $mediumLevel = new Level();
        $mediumLevel->setName('Medium')->setColor('#f6b93b');
        $manager->persist($mediumLevel);
        $mediumLevels[] = $mediumLevel;

        $lowLevels = [];
        $lowLevel = new Level();
        $lowLevel->setName('Low')->setColor('#4a69bd');
        $manager->persist($lowLevel);
        $lowLevels[] = $lowLevel;

        // Create Status
        $newStatus = [];
        $newStatu = new Status();
        $newStatu->setName('New')->setColor('#eb2f06');
        $manager->persist($newStatu);
        $newStatus[] = $newStatu;

        $openStatus = [];
        $openStatu = new Status();
        $openStatu->setName('Open')->setColor('#f6b93b');
        $manager->persist($openStatu);
        $openStatus[] = $openStatu;

        $closedStatus = [];
        $closedStatu = new Status();
        $closedStatu->setName('Closed')->setColor('#78e08f')->setIsClose(true);
        $manager->persist($closedStatu);
        $closedStatus[] = $closedStatu;

        // Create 10 Tickets
        $tickets = [];
        for ($i = 0; $i <= 10; ++$i) {
            $ticket = (new Ticket())
                ->setContent($this->faker()->paragraphs(10, true))
                ->setSubject($this->faker()->word())
                ->setUser($this->faker()->randomElement($users))
                ->setStatus(
                    1 === mt_rand(0, 2) ?
                    $this->faker()->randomElement($openStatus) : $this->faker()->randomElement($closedStatus)
                )
                ->setLevel(
                    1 === mt_rand(0, 2) ?
                    $this->faker()->randomElement($lowLevels) : $this->faker()->randomElement($mediumLevels)
                )
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($ticket);
            $tickets[] = $ticket;
        }

        // Create 10 Responses
        $responses = [];
        for ($i = 0; $i <= 10; ++$i) {
            $response = (new Response())
                ->setContent($this->faker()->paragraphs(10, true))
                ->setTicket($this->faker()->randomElement($tickets))
                ->setUser($this->faker()->randomElement($users))
                ->setCreatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
                ->setUpdatedAt(\DateTimeImmutable::createFromInterface($this->faker()->dateTimeBetween('-50 days', '+10 days')))
            ;

            $manager->persist($response);
            $responses[] = $response;
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [
            AppUserFixtures::class,
        ];
    }
}
