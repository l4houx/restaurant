<?php

namespace App\Repository;

use App\Entity\PostType;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PostType>
 */
class PostTypeRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, PostType::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.updatedAt <= :now')
            ->orWhere('p.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::POST_TYPE_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name'],
            ]
        );
    }

    public function getPostsTypes($isOnline, $keyword, $slug, $limit, $sort, $order, $hasposts): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->addSelect('COUNT(p) as HIDDEN postscount');

        if ('all' !== $isOnline) {
            $qb->andWhere('p.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
        }

        if ('all' !== $keyword) {
            $qb->andWhere('p.name LIKE :keyword or :keyword LIKE p.name')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $slug) {
            $qb->andWhere('p.slug = :slug')->setParameter('slug', $slug);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (true === $hasposts || 1 === $hasposts) {
            $qb->join('p.posts', 'posts');
        }

        $qb->groupBy('p');

        return $qb;
    }
}
