<?php

namespace App\Repository\Data;

use App\Entity\Company\Member;
use App\Entity\Data\Account;
use App\Entity\User\Collaborator;
use App\Entity\User\Customer;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
    public function getClientsAccountByEmployee(SalesPerson|Manager|SuperAdministrator $employee): array
    {
        $members = [$employee->getMember()->getId()];

        if ($employee instanceof Manager && $employee instanceof SuperAdministrator) {
            $members = $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray();
        }

        $customerAccountsQB = $this->createQueryBuilder('c')
            ->select('account1.id')
            ->from(Customer::class, 'customer')
            ->join('customer.account', 'account1')
            ->join('customer.client', 'client')
            ->where('client.member IN (:members)')
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder('a')
            ->addSelect('u')
            ->addSelect('w')
            ->join('a.user', 'u')
            ->leftJoin('a.wallets', 'w')
            ->setParameter('members', $members)
        ;

        return $qb
            ->andWhere($qb->expr()->in('a.id', $customerAccountsQB))
            ->getQuery()
            ->getResult()
        ;
    }

    public function createQueryBuilderAccountByManagerForTransfer(Manager|SuperAdministrator $manager): QueryBuilder
    {
        $customerAccountsQB = $this->createQueryBuilder('c')
            ->select('account1.id')
            ->from(Customer::class, 'customer')
            ->leftJoin('customer.account', 'account1')
            ->join('customer.client', 'client')
            ->where('client.member IN (:members)')
            ->getDQL()
        ;

        $collaboratorAccountsQB = $this->createQueryBuilder('co')
            ->select('account2.id')
            ->from(Collaborator::class, 'collaborator')
            ->join('collaborator.account', 'account2')
            ->where('collaborator.member IN (:members)')
            ->getDQL()
        ;

        $salesPersonAccountsQB = $this->createQueryBuilder('sa')
            ->select('account3.id')
            ->from(SalesPerson::class, 'salesPerson')
            ->join('salesPerson.account', 'account3')
            ->where('salesPerson.member IN (:members)')
            ->getDQL()
        ;

        $managerAccountsQB = $this->createQueryBuilder('ma')
            ->select('account4.id')
            ->from(Manager::class, 'manager')
            ->join('manager.account', 'account4')
            ->where('manager.member IN (:members)')
            ->getDQL()
        ;

        $membersAccountsQB = $this->createQueryBuilder('me')
            ->select('account5.id')
            ->from(Member::class, 'm2')
            ->join('m2.account', 'account5')
            ->where('m2 IN (:members)')
            ->getDQL()
        ;

        $superadministratorQB = $this->createQueryBuilder('su')
            ->select('account6.id')
            ->from(SuperAdministrator::class, 'superadministrator')
            ->join('superadministrator.account', 'account6')
            ->where('superadministrator IN (:members)')
            ->getDQL()
        ;

        $qb = $this->createQueryBuilder('a')
            ->addSelect('u')
            ->addSelect('m')
            ->addSelect('w')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.member', 'm')
            ->leftJoin('a.wallets', 'w')
            ->setParameter('members', $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray())
        ;

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->in('a.id', $customerAccountsQB),
                $qb->expr()->in('a.id', $collaboratorAccountsQB),
                $qb->expr()->in('a.id', $salesPersonAccountsQB),
                $qb->expr()->in('a.id', $managerAccountsQB),
                $qb->expr()->in('a.id', $membersAccountsQB),
                $qb->expr()->in('a.id', $superadministratorQB)
            )
        );

        return $qb;
    }

    public function createQueryBuilderAccountByManagerForPurchase(Manager|SuperAdministrator $manager): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->addSelect('m')
            ->join('a.member', 'm')
            ->where('m IN (:members)')
            ->setParameter(
                'members',
                $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
            )
        ;
    }
}
