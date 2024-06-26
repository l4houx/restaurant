<?php

namespace App\Repository\Tickets;

use App\Entity\Tickets\Level;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Level>
 */
class LevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Level::class);
    }

    /**
     * Returns the levels after applying the specified search criterias.
     *
     * @param string                  $keyword
     * @param int                     $id
     * @param int                     $limit
     * @param string                  $order
     * @param string                  $sort
     *
     * @return QueryBuilder<Level> (LevelController)
     */
    public function getLevels($keyword, $id, $limit, $order, $sort): QueryBuilder
    {
        $qb = $this->createQueryBuilder('l');

        $qb->select('l');

        if ('all' !== $keyword) {
            $qb->andWhere('l.name LIKE :keyword or :keyword LIKE l.name or :keyword LIKE l.color or l.color LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
        }

        if ('all' !== $id) {
            $qb->andWhere('l.id = :id')->setParameter('id', $id);
        }

        if ('all' !== $limit) {
            $qb->setMaxResults($limit);
        }

        if ($sort) {
            $qb->orderBy('l.'.$sort, $order);
        }

        return $qb;
    }
}
