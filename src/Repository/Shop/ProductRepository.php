<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Category;
use App\Entity\Shop\Filter;
use App\Entity\Shop\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Paginator<Product>
     */
    public function getPaginated(
        int $page,
        int $limit,
        string $sort,
        ?Category $category,
        Filter $filter
    ): Paginator {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->andWhere('p.amount >= :min')
            ->setParameter('min', $filter->min)
            ->andWhere('p.amount <= :max')
            ->setParameter('max', $filter->max)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        /*if ($category !== null) {
            $qb
                ->andWhere("c.left >= :left")
                ->andWhere("c.right <= :right")
                ->setParameter("left", $category->getLeft())
                ->setParameter("right", $category->getRight())
            ;
        }*/

        if (null !== $filter->brand) {
            $qb
                ->andWhere('b = :brand')
                ->setParameter('brand', $filter->brand)
            ;
        }

        if (null !== $filter->keywords) {
            $qb
                ->andWhere("CONCAT(p.name, ' ', p.content, ' ', b.name) LIKE :keywords")
                ->setParameter('keywords', $filter->keywords)
            ;
        }

        switch ($sort) {
            case 'amount-asc':
                $qb->orderBy('p.amount', 'asc');
                break;
            case 'amount-desc':
                $qb->orderBy('p.amount', 'desc');
                break;
            case 'name-asc':
                $qb->orderBy('p.name', 'asc');
                break;
            case 'name-desc':
                $qb->orderBy('p.name', 'desc');
                break;
            default:
                $qb->orderBy('lp.id', 'desc')->orderBy('p.id', 'desc');
                break;
        }

        return new Paginator($qb);
    }

    /**
     * @return array<Product>
     */
    public function getLastProducts(int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->join('p.brand', 'b')
            ->join('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->setMaxResults($limit)
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
