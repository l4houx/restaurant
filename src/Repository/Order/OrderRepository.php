<?php

namespace App\Repository\Order;

use App\Entity\Order\Order;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Member;
use App\Entity\Traits\HasLimit;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return array<int, Order>
     */
    public function getOrdersByMemberEmployee(SalesPerson|Manager|SuperAdministrator $employee): array
    {
        $qb = $this->createQueryBuilder('o')
            ->join('o.user', 'u')
            ->orderBy('o.createdAt', 'desc')
        ;

        if ($employee instanceof SalesPerson) {
            $subQuery = $this->createQueryBuilder('c')
                ->select('c.id')
                ->from(Customer::class, 'c')
                ->join('c.client', 'cl')
                ->where('cl.salesPerson = :employee')
                ->getDQL()
            ;

            $qb->setParameter('employee', $employee);
        } else {
            $subQuery = $this->createQueryBuilder('c')
                ->select('c.id')
                ->from(Customer::class, 'c')
                ->join('c.client', 'cl')
                ->join('cl.member', 'm')
            ;

            $subQuery = $subQuery
                ->where($subQuery->expr()->in(
                    'm.id',
                    $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                ))
                ->getDQL()
            ;
        }

        return $qb
            ->andWhere($qb->expr()->in('u.id', $subQuery))
            ->getQuery()
            ->getResult()
        ;
    }
}
