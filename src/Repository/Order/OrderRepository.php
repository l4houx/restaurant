<?php

namespace App\Repository\Order;

use App\Entity\Company\Member;
use App\Entity\Order\Order;
use App\Entity\Traits\HasLimit;
use App\Entity\User\Customer;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

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

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('o.updatedAt <= :now')
            // ->orWhere('s.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::ORDER_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['o.id', 'o.firstname', 'o.lastname', 'o.reference'],
            ]
        );
    }

    /**
     * @return array<int, Order>
     */
    public function getOrdersByMemberEmployee(SalesPerson|Manager $employee): array
    {
        $qb = $this->createQueryBuilder('o')
            ->join('o.user', 'u')
            ->orderBy('o.createdAt', 'desc')
        ;

        if ($employee instanceof SalesPerson) {
            $subQuery = $this->_em->createQueryBuilder()
                ->select('c.id')
                ->from(Customer::class, 'c')
                ->join('c.client', 'cl')
                ->where('cl.salesPerson = :employee')
                ->getDQL()
            ;

            $qb->setParameter('employee', $employee);
        } else {
            $subQuery = $this->_em->createQueryBuilder()
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
