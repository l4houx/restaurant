<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Brand;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Brand>
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Brand::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('b')
            ->orderBy('b.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('b.updatedAt <= :now')
            ->orWhere('b.isActive = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::BRAND_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['b.id', 'b.name'],
            ]
        );
    }
}
