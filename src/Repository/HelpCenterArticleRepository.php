<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use App\Entity\HelpCenterArticle;
use App\Entity\HelpCenterCategory;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<HelpCenterArticle>
 */
class HelpCenterArticleRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, HelpCenterArticle::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('a')
            ->addSelect('c')
            ->join('a.category', 'c')
            ->orderBy('a.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('a.updatedAt <= :now')
            ->orWhere('a.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::HELPCENTERARTICLE_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['a.id', 'a.name'],
            ]
        );
    }

    /**
     * Returns the help center articles after applying the specified search criterias.
     *
     * @param string             $selecttags
     * @param bool               $isOnline
     * @param bool               $isFeatured
     * @param string             $keyword
     * @param string             $slug
     * @param HelpCenterCategory $category
     * @param int                $limit
     * @param string             $sort
     * @param string             $order
     * @param string             $otherthan
     *
     * @return QueryBuilder<HelpCenterArticle> (HelpCenterArticleController)
     */
    public function getHelpCenterArticles($selecttags, $isOnline, $isFeatured, $keyword, $slug, $category, $limit, $sort, $order, $otherthan): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        if (!$selecttags) {
            $qb->select('a');

            if ('all' !== $isOnline) {
                $qb->andWhere('a.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
            }

            if ('all' !== $isFeatured) {
                $qb->andWhere('a.isFeatured = :isFeatured')->setParameter('isFeatured', $isFeatured);
            }

            if ('all' !== $keyword) {
                $qb->andWhere('a.name LIKE :keyword or :keyword LIKE a.name or :keyword LIKE a.content or a.content LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
            }

            if ('all' !== $slug) {
                $qb->andWhere('a.slug = :slug')->setParameter('slug', $slug);
            }

            if ('all' !== $category) {
                $qb->leftJoin('a.category', 'category');
                $qb->andWhere('category.slug = :category')->setParameter('category', $category);
            }

            if ('all' !== $limit) {
                $qb->setMaxResults($limit);
            }

            if ('all' !== $otherthan) {
                $qb->andWhere('a.slug != :otherthan')->setParameter('otherthan', $otherthan);
                $qb->andWhere('a.slug = :otherthan')->setParameter('otherthan', $otherthan);
            }

            $qb->orderBy('a.'.$sort, $order);
        } else {
            $qb->select("SUBSTRING_INDEX(GROUP_CONCAT(a.tags SEPARATOR ','), ',', 8)");
        }

        return $qb;
    }
}
