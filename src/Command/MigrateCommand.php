<?php

namespace App\Command;

use App\Entity\User;
use DateTimeImmutable;
use League\Csv\Reader;
use League\Csv\Statement;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Service\AvatarService;
use App\Entity\User\SalesPerson;
use App\Entity\Company\Organization;
use App\Entity\User\SuperAdministrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:migrate',
    description: 'Migrate from old database to a new one.',
)]
class MigrateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AvatarService $avatarService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::getDefaultDescription())
            ->addArgument('users-file', InputArgument::REQUIRED, 'Path to users CSV file.')
            ->addArgument('organizations-file', InputArgument::REQUIRED, 'Path to organizations CSV file.')
            ->addArgument('members-file', InputArgument::REQUIRED, 'Path to members CSV file.')
            ->addArgument('clients-file', InputArgument::REQUIRED, 'Path to clients CSV file.')
            ->addArgument('addresses-file', InputArgument::REQUIRED, 'Path to addresses CSV file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $originalUsers = $this->convertUsers($input->getArgument('users-file'));
        $originalOrganizations = $this->convertUsers($input->getArgument('organizations-file'));
        $originalMembers = $this->convertUsers($input->getArgument('members-file'));
        $originalClients = $this->convertUsers($input->getArgument('clients-file'));
        $originalAddresses = $this->convertUsers($input->getArgument('addresses-file'));

        // Organizations
        /** @var array<int, Organization> $organizations */
        $organizations = [];

        $io->info('Organizations migration !');
        $progressBar = new ProgressBar($output, count($originalOrganizations));
        $progressBar->start();

        foreach ($originalOrganizations as $id => $originalOrganization) {
            $progressBar->advance();
            $organization = new Organization();
            $organization->setName($originalOrganization["name"]);
            $organization->setCompanyNumber($originalOrganization["siret"]);
            $this->em->persist($organization);
            $organizations[$id] = $organization;
        }
        $progressBar->finish();

        // Member
        /** @var array<int, Member> $members */
        $members = [];

        $io->info('Members migration !');
        $progressBar = new ProgressBar($output, count($originalMembers));
        $progressBar->start();

        foreach ($originalMembers as $id => $originalMember) {
            $progressBar->advance();
            $originalAddress = $originalAddresses[$originalMember["address_id"]];
            $member = new Member();
            $member->setOrganization($organizations[$originalMember["organization_id"]]);
            $member->setName($originalMember["name"]);
            $member->setCompanyNumber($originalMember["siret"]);
            $member->getAddress()->setPhone($originalAddress["phone"]);
            $member->getAddress()->setStreetAddress($originalAddress["street_address"]);
            $member->getAddress()->setEmail($originalAddress["email"]);
            $member->getAddress()->setZipCode($originalAddress["zip_code"]);
            $member->getAddress()->setLocality($originalAddress["locality"]);
            $member->getAddress()->setRestAddress($originalAddress["rest_address"]);
            $this->em->persist($member);
            $members[$id] = $member;
        }
        $progressBar->finish();

        // Client
        /** @var array<int, Client> $clients */
        $clients = [];

        $io->info('Clients migration !');
        $progressBar = new ProgressBar($output, count($originalClients));
        $progressBar->start();

        foreach ($originalClients as $id => $originalClient) {
            $progressBar->advance();
            $originalAddress = $originalAddresses[$originalClient["address_id"]];
            $client = new Client();
            $client->setMember($members[$originalClient["member_id"]]);
            $client->setName($originalClient["name"]);
            $client->setCompanyNumber($originalClient["siret"]);
            $client->setDeletedAt($originalClient["deleted_at"] === "" ? null : new \DateTimeImmutable());
            $client->getAddress()->setPhone($originalAddress["phone"]);
            $client->getAddress()->setStreetAddress($originalAddress["street_address"]);
            $client->getAddress()->setEmail($originalAddress["email"]);
            $client->getAddress()->setZipCode($originalAddress["zip_code"]);
            $client->getAddress()->setLocality($originalAddress["locality"]);
            $client->getAddress()->setRestAddress($originalAddress["rest_address"]);
            $this->em->persist($client);
            $clients[$id] = $client;
        }
        $progressBar->finish();

        // User
        /** @var array<int, User> $users */
        $users = [];

        $io->info('Users migration !');
        $progressBar = new ProgressBar($output, count($originalUsers));
        $progressBar->start();

        foreach ($originalUsers as $id => $originalUser) {
            $progressBar->advance();
            $user = match ($originalUser["discr"]) {
                "superadministrator" => new SuperAdministrator(),
                "manager" => new Manager(),
                "sales_person" => new SalesPerson(),
                default => new Customer()
            };

            /** @var User $user */
            $user->setAvatar($this->avatarService->createAvatar($originalUser["email"]));
            $user->setUsername($originalUser["username"]);
            $user->setPassword("password");
            $user->setDeletedAt($originalUser["deleted_at"] === "" ? null : new DateTimeImmutable());
            $user->setEmail($originalUser["email"]);
            $user->setLastName($originalUser["lastname"]);
            $user->setFirstName($originalUser["firstname"]);
            $user->setIsSuspended($originalUser["isSuspended"] === "1");
            $user->setIsVerified($originalUser["isVerified"] === "1");
            $user->setIsAgreeTerms($originalUser["isAgreeTerms"] === "1");
            $user->setLastLogin(
                $originalUser["last_login"] === ""
                    ? null
                    : new DateTimeImmutable($originalUser["last_login"])
            );

            if ($user instanceof Customer) {
                $user->setManualDelivery($originalUser["manual_delivery"] === "1");
                if (!isset($clients[$originalUser["company_id"]])) {
                    $io->warning(sprintf("User %s:%s was not imported !", $originalUser["id"], $user->getFullName()));
                    continue;
                }
                $user->setClient($clients[$originalUser["company_id"]]);
            } else {
                /** @var SalesPerson|Manager|SuperAdministrator $user */
                $user->setPhone("0100000000");
                if (!isset($members[$originalUser["company_id"]])) {
                    $io->warning(sprintf("User %s:%s was not imported !", $originalUser["id"], $user->getFullName()));
                    continue;
                }
                $user->setMember($members[$originalUser["company_id"]]);
            }

            $this->em->persist($user);
            $users[$originalUser["id"]] = $user;
        }
        $progressBar->finish();

        // Client
        $io->info('Attach sales person to clients !');
        $progressBar = new ProgressBar($output, count($clients));
        $progressBar->start();

        foreach ($clients as $id => $client) {
            $progressBar->advance();
            if (
                $originalClients[$id]["sales_person_id"] !== ""
                && isset($users[$originalClients[$id]["sales_person_id"]])
                && $users[$originalClients[$id]["sales_person_id"]] instanceof SalesPerson
            ) {
                $client->setSalesPerson($users[$originalClients[$id]["sales_person_id"]]);
            }
        }
        $progressBar->finish();

        $this->em->flush();

        $io->success('Migration complete !');

        return Command::SUCCESS;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function convertUsers(string $filePath): array
    {
        $reader = Reader::createFromPath($filePath);
        $reader->setHeaderOffset(0);
        $records = Statement::create()->process($reader);

        $data = iterator_to_array($records->getRecords());

        return array_combine(
            array_column($data, "id"),
            $data
        );
    }
}
