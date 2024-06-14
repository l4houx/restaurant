<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Filter;
use App\Entity\Shop\Product;
use App\Entity\Shop\Category;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findForPagination(
        int $page,
        int $limit,
        string $sort,
        ?Category $category,
        Filter $filter
    ): PaginationInterface {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->join('p.brand', 'b')
            ->join('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->andWhere('p.amount >= :min')
            ->setParameter('min', $filter->min)
            ->andWhere('p.amount <= :max')
            ->setParameter('max', $filter->max)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        if (null !== $category) {
            $builder
                ->andWhere('c.left >= :left')
                ->andWhere('c.right <= :right')
                ->setParameter('left', $category->getLeft())
                ->setParameter('right', $category->getRight())
            ;
        }

        if (null !== $filter->brand) {
            $builder
                ->andWhere('b = :brand')
                ->setParameter('brand', $filter->brand)
            ;
        }

        if (null !== $filter->keywords) {
            $builder
                ->andWhere("CONCAT(p.name, ' ', p.content, ' ', b.name) LIKE :keywords")
                ->setParameter('keywords', $filter->keywords)
            ;
        }

        switch ($sort) {
            case 'amount-asc':
                $builder->orderBy('p.amount', 'asc');
                break;
            case 'amount-desc':
                $builder->orderBy('p.amount', 'desc');
                break;
            case 'name-asc':
                $builder->orderBy('p.name', 'asc');
                break;
            case 'name-desc':
                $builder->orderBy('p.name', 'desc');
                break;
            default:
                $builder->orderBy('lp.id', 'desc')->orderBy('p.id', 'desc');
                break;
        }

        // return new Paginator($builder);

        return $this->paginator->paginate(
            $builder,
            $page,
            $limit
        );
    }

    /**
     * @return array<Product>
     */
    public function getLastProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->join('p.brand', 'b')
            ->join('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->setMaxResults(4)
            ->orderBy('lp.id', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMinAmount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.amount')
            ->where('p.isOnline = true')
            ->orderBy('p.amount', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getMaxAmount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.amount')
            ->where('p.isOnline = true')
            ->orderBy('p.amount', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
