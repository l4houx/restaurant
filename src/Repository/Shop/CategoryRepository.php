<?php

namespace App\Repository\Shop;

use App\Entity\Shop\Category;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return array<Category>
     */
    public function getLastCategories(int $limit): array
    {
        return $this->createQueryBuilder('c')

            ->setMaxResults($limit)
            ->orderBy('c.numberOfProducts', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            //->setParameter('now', new \DateTimeImmutable())
            //->where('c.updatedAt <= :now')
            //->orWhere('c.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::CATEGORY_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['c.id', 'c.name'],
            ]
        );
    }

    /**
     * Returns the categories after applying the specified search criterias.
     *
     * @param bool   $isOnline
     * @param string $keyword
     * @param int    $id
     * @param int    $limit
     * @param string $sort
     * @param string $order
     * 
     * @return QueryBuilder<Category>
     */
    public function getCategories($isOnline, $keyword, $id, $limit, $sort, $order): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('DISTINCT c');

        if ($isOnline !== "all") {
            $qb->andWhere('c.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
        }

        if ($keyword !== "all") {
            $qb->andWhere('c.name LIKE :keyword or :keyword LIKE c.name')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ($id !== "all") {
            $qb->andWhere('c.id = :id')->setParameter('id', $id);
        }

        if ($limit !== "all") {
            $qb->setMaxResults($limit);
        }

        $qb->orderBy($sort, $order);

        return $qb;
    }
}
