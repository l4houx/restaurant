<?php

namespace App\Repository;

use App\Entity\Testimonial;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Testimonial>
 */
class TestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimonial::class);
    }

    /**
     * @return Paginator<Testimonial>
     */
    public function getPaginated(
        int $page,
        int $limit,
        ?string $keywords
    ): Paginator {
        $qb = $this->createQueryBuilder('t')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('t.createdAt <= :now')
            ->orWhere('t.isOnline = true')
            ->andWhere('t.name LIKE :keywords')
            ->setParameter('keywords', '%'.($keywords ?? '').'%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
        ;

        return new Paginator($qb);
    }

    /**
     * Retrieves testimonials randomly (-1 months)
     */
    public function getRand(int $limit): array
    {
        $date = new \DateTimeImmutable('1 day');

        return $this->createQueryBuilder('t')
            ->addSelect('a')
            ->join('t.author', 'a')
            ->where('t.isOnline = true')
            ->andWhere('t.createdAt < :date')
            ->setParameter('date', $date)
            ->setMaxResults($limit)
            ->orderBy('RAND()')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Testimonial>
     */
    public function getLastTestimonials(int $limit): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('a')
            ->join('t.author', 'a')
            ->where('t.isOnline = true')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('t.createdAt <= :now')
            ->setMaxResults($limit)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retrieves the latest testimonials created by the user.
     *
     * @return Testimonial[] Returns an array of Testimonial objects
     */
    public function getLastByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.isOnline = true')
            ->andWhere('t.author = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the testimonials after applying the specified search criterias.
     *
     * @param string    $keyword
     * @param string    $slug
     * @param User|null $user
     * @param bool      $isOnline
     * @param int|null  $rating
     * @param int       $minrating
     * @param int       $maxrating
     * @param int       $limit
     * @param int       $count
     * @param string    $sort
     * @param string    $order
     */
    public function getTestimonials($keyword, $slug, $user, $isOnline, $rating, $minrating, $maxrating, $limit, $count, $sort, $order): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        if ($count) {
            $qb->select('COUNT(t)');
        } else {
            $qb->select('t');
        }

        if ('all' !== $keyword) {
            $qb->andWhere('t.name LIKE :keyword or :keyword LIKE t.name or t.content LIKE :keyword or :keyword LIKE t.content')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $slug) {
            $qb->andWhere('t.slug = :slug')->setParameter('slug', $slug);
        }

        if ('all' !== $user) {
            $qb->leftJoin('t.author', 'user');
            $qb->andWhere('user.id = :user')->setParameter('user', $user);
        }

        if ('all' !== $isOnline) {
            $qb->andWhere('t.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
        }

        if ('all' !== $rating) {
            $qb->andWhere('t.rating = :rating')->setParameter('rating', $rating);
        }

        if ('all' !== $minrating) {
            $qb->andWhere('t.rating >= :minrating')->setParameter('minrating', $minrating);
        }

        if ('all' !== $maxrating) {
            $qb->andWhere('t.rating <= :maxrating')->setParameter('maxrating', $maxrating);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if ($sort) {
            $qb->orderBy('t.'.$sort, $order);
        }

        return $qb;
    }
}
