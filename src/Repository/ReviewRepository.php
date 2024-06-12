<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Retrieves the latest reviews created by the user.
     *
     * @return Review[] Returns an array of Review objects
     */
    public function findLastByUser(User $user, int $maxResults): array //  (UserController)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.author = :user')
            ->andWhere('r.isVisible = true')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($maxResults)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the reviews after applying the specified search criterias.
     *
     * @param string          $keyword
     * @param string          $slug
     * @param User            $user
     * @param Product|null    $product
     * @param bool            $isVisible
     * @param int|null        $rating
     * @param int             $minrating
     * @param int             $maxrating
     * @param int             $limit
     * @param int             $count
     * @param string          $sort
     * @param string          $order
     */
    public function getReviews($keyword, $slug, $user, $product, $isVisible, $rating, $minrating, $maxrating, $limit, $count, $sort, $order): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r');

        if ($count) {
            $qb->select('COUNT(r)');
        } else {
            $qb->select('r');
        }

        if ('all' !== $keyword) {
            $qb->andWhere('r.name LIKE :keyword or :keyword LIKE r.name or r.content LIKE :keyword or :keyword LIKE r.content')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $slug) {
            $qb->andWhere('r.slug = :slug')->setParameter('slug', $slug);
        }

        if ('all' !== $user) {
            $qb->leftJoin('r.author', 'user');
            $qb->andWhere('user.slug = :user')->setParameter('user', $user);
        }

        if ('all' !== $product) {
            $qb->leftJoin('r.product', 'product');
        }

        if ('all' !== $product) {
            $qb->leftJoin('product.id', 'product');
            $qb->andWhere('product.slug = :product')->setParameter('product', $product);
        }

        if ('all' !== $isVisible) {
            $qb->andWhere('r.isVisible = :isVisible')->setParameter('isVisible', $isVisible);
        }

        if ('all' !== $rating) {
            $qb->andWhere('r.rating = :rating')->setParameter('rating', $rating);
        }

        if ('all' !== $minrating) {
            $qb->andWhere('r.rating >= :minrating')->setParameter('minrating', $minrating);
        }

        if ('all' !== $maxrating) {
            $qb->andWhere('r.rating <= :maxrating')->setParameter('maxrating', $maxrating);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if ($sort) {
            $qb->orderBy('r.'.$sort, $order);
        }

        return $qb;
    }
}
