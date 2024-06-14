<?php

namespace App\Repository\Data;

use App\Entity\Data\Account;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Member;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User\SalesPerson;
use App\Entity\User\Collaborator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return array<int, Account>
     */
    public function getClientsAccountByEmployee(SalesPerson | Manager $employee): array
    {
        $members = [$employee->getMember()->getId()];

        if ($employee instanceof Manager) {
            $members = $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray();
        }

        $customerAccountsQB = $this->_em->createQueryBuilder()
            ->select("account1.id")
            ->from(Customer::class, "customer")
            ->join("customer.account", "account1")
            ->join("customer.client", "client")
            ->where("client.member IN (:members)")
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder("a")
            ->addSelect("u")
            ->addSelect("w")
            ->join("a.user", "u")
            ->leftJoin("a.wallets", "w")
            ->setParameter("members", $members)
        ;

        return $qb
            ->andWhere($qb->expr()->in("a.id", $customerAccountsQB))
            ->getQuery()
            ->getResult()
        ;
    }

    public function createQueryBuilderAccountByManagerForTransfer(Manager $manager): QueryBuilder
    {
        $customerAccountsQB = $this->_em->createQueryBuilder()
            ->select("account1.id")
            ->from(Customer::class, "customer")
            ->join("customer.account", "account1")
            ->join("customer.client", "client")
            ->where("client.member IN (:members)")
            ->getDQL()
        ;

        $collaboratorAccountsQB = $this->_em->createQueryBuilder()
            ->select("account2.id")
            ->from(Collaborator::class, "collaborator")
            ->join("collaborator.account", "account2")
            ->where("collaborator.member IN (:members)")
            ->getDQL()
        ;

        $salesPersonAccountsQB = $this->_em->createQueryBuilder()
            ->select("account3.id")
            ->from(SalesPerson::class, "salesPerson")
            ->join("salesPerson.account", "account3")
            ->where("salesPerson.member IN (:members)")
            ->getDQL()
        ;

        $managerAccountsQB = $this->_em->createQueryBuilder()
            ->select("account4.id")
            ->from(Manager::class, "manager")
            ->join("manager.account", "account4")
            ->where("manager.member IN (:members)")
            ->getDQL()
        ;

        $membersAccountsQB = $this->_em->createQueryBuilder()
            ->select("account5.id")
            ->from(Member::class, "m2")
            ->join("m2.account", "account5")
            ->where("m2 IN (:members)")
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder("a")
            ->addSelect("u")
            ->addSelect("m")
            ->addSelect("w")
            ->leftJoin("a.user", "u")
            ->leftJoin("a.member", "m")
            ->leftJoin("a.wallets", "w")
            ->setParameter("members", $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray())
        ;

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in("a.id", $customerAccountsQB),
                $qb->expr()->in("a.id", $collaboratorAccountsQB),
                $qb->expr()->in("a.id", $salesPersonAccountsQB),
                $qb->expr()->in("a.id", $managerAccountsQB),
                $qb->expr()->in("a.id", $membersAccountsQB)
            )
        );

        return $qb;
    }

    public function createQueryBuilderAccountByManagerForPurchase(Manager $manager): QueryBuilder
    {
        return $this->createQueryBuilder("a")
            ->addSelect("m")
            ->join("a.member", "m")
            ->where("m IN (:members)")
            ->setParameter(
                "members",
                $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
            )
        ;
    }
}
