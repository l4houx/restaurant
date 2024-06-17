<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\PostCategory;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use function Symfony\Component\String\u;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Post::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.publishedAt <= :now')
            ->orWhere('p.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::POST_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name'],
            ]
        );
    }

    public function searchPost(string $keyword): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('LOWER(p.name) LIKE :keyword OR LOWER(p.content) LIKE :keyword')
            ->setParameter('keyword', '%'.mb_strtolower($keyword).'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMostCommented(int $maxResults): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.comments', 'c')
            ->addSelect('COUNT(c) AS HIDDEN numberOfComments')
            ->andWhere('c.isApproved = true')
            ->groupBy('p')
            ->orderBy('numberOfComments', 'DESC')
            ->addOrderBy('p.publishedAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Post>
     */
    public function getLastPosts(int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('t')
            ->addSelect('c')
            ->join('p.type', 't')
            ->join('p.category', 'c')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.publishedAt <= :now')
            ->setMaxResults($limit)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the blog posts after applying the specified search criterias.
     *
     * @param string            $selecttags
     * @param bool              $isOnline
     * @param string            $keyword
     * @param string            $slug
     * @param PostCategory|null $category
     * @param int               $limit
     * @param string            $sort
     * @param string            $order
     * @param string            $otherthan
     *
     * @return QueryBuilder<Post> (PostController)
     */
    public function getBlogPosts($selecttags, $isOnline, $keyword, $slug, $category, $limit, $sort, $order, $otherthan): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if (!$selecttags) {
            $qb->select('p');
            if ('all' !== $isOnline) {
                $qb->andWhere('p.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
            }
            if ('all' !== $keyword) {
                $qb->andWhere('p.name LIKE :keyword or :keyword LIKE p.name or :keyword LIKE p.content or p.content LIKE :keyword or :keyword LIKE p.tags or p.tags LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
            }
            if ('all' !== $slug) {
                $qb->andWhere('p.slug = :slug')->setParameter('slug', $slug);
            }
            if ('all' !== $category) {
                $qb->leftJoin('p.category', 'category');
                $qb->andWhere('category.slug = :category')->setParameter('category', $category);
            }
            if ('all' !== $limit) {
                $qb->setMaxResults($limit);
            }
            if ('all' !== $otherthan) {
                $qb->andWhere('p.slug != :otherthan')->setParameter('otherthan', $otherthan);
                $qb->andWhere('p.slug = :otherthan')->setParameter('otherthan', $otherthan);
            }
            $qb->orderBy('p.'.$sort, $order);
        } else {
            $qb->select("SUBSTRING_INDEX(GROUP_CONCAT(p.tags SEPARATOR ','), ',', 8)");
        }

        return $qb;
    }

    public function findPrevious(Post $post): ?Post
    {
        return $this->createQueryBuilder('p')
            //->where('p.isOnline = true AND p.publishedAt < NOW()')
            ->where('p.isOnline = true')
            ->andWhere('p.publishedAt < :postPublishedAt')
            ->setParameter('postPublishedAt', $post->getPublishedAt())
            ->orderBy('p.publishedAt', 'DESC')
            //->groupBy('p.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNext(Post $post): ?Post
    {
        return $this->createQueryBuilder('p')
            //->where('p.isOnline = true AND p.publishedAt < NOW()')
            ->where('p.isOnline = true')
            ->andWhere('p.publishedAt < :postPublishedAt')
            ->setParameter('postPublishedAt', $post->getPublishedAt())
            ->orderBy('p.publishedAt', 'ASC')
            //->groupBy('p.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Post[] returns an array of Post objects similar with the given post
     */
    public function findSimilar(Post $post, int $maxResults = 4): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->addSelect('COUNT(t) AS HIDDEN numberOfTags')
            ->andWhere('t IN (:tags)')
            ->andWhere('p != :post')
            ->setParameter('tags', $post->getTags())
            ->setParameter('post', $post,)
            ->groupBy('p')
            ->addOrderBy('numberOfTags', 'DESC')
            ->addOrderBy('p.publishedAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findBySearchQuery(string $query, int $maxResults = HasLimit::POST_LIMIT): array
    {
        $search = $this->search($query);

        if (0 === \count($search)) {
            return [];
        }

        $qb = $this->createQueryBuilder('p');

        foreach ($search as $key => $value) {
            $qb
                ->orWhere('p.name LIKE :v_'.$key)
                ->setParameter('v_'.$key, '%'.$value.'%')
            ;
        }

        /** @var Post[] $result */
        $result = $qb
            ->orderBy('p.publishedAt', 'DESC')
            ->orWhere('p.isOnline = true')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @return string[]
     */
    private function search(string $searchQuery): array
    {
        $values = array_unique(u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim()->split(' '));

        return array_filter($values, static function ($value) {
            return 2 <= $value->length();
        });
    }
}
