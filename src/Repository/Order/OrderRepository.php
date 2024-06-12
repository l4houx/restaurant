<?php

namespace App\Repository\Order;

use App\Entity\Order\Order;
use App\Entity\Traits\HasLimit;
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
}
