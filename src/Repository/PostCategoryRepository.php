<?php

namespace App\Repository;

use App\Entity\PostCategory;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PostCategory>
 */
class PostCategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, PostCategory::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('c')
            ->leftJoin('c.posts', 'p')
            ->select('c', 'p')
            ->orderBy('c.createdAt', 'DESC')
            ->orWhere('c.isOnline = true'),
            $page,
            HasLimit::POSTCATEGORY_LIMIT,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['c.id', 'c.name'],
            ]
        );
    }

    /**
     * Returns the blog posts categories after applying the specified search criterias.
     *
     * @param bool   $isOnline
     * @param string $keyword
     * @param string $slug
     * @param int    $limit
     * @param string $order
     * @param string $sort
     *
     * @return QueryBuilder<PostCategory>
     */
    public function getBlogPostCategories($isOnline, $keyword, $slug, $limit, $order, $sort): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c');

        if ('all' !== $isOnline) {
            $qb->andWhere('c.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
        }

        if ('all' !== $keyword) {
            $qb->andWhere('c.name LIKE :keyword or :keyword LIKE c.name')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $slug) {
            $qb->andWhere('c.slug = :slug')->setParameter('slug', $slug);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if ('postscount' == $order) {
            $qb->leftJoin('c.posts', 'posts');
            $qb->addSelect('COUNT(posts.id) AS HIDDEN postscount');
            $qb->orderBy('postscount', 'DESC');
            $qb->groupBy('c.id');
        } else {
            $qb->orderBy($order, $sort);
        }

        return $qb;
    }
}
