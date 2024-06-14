<?php

namespace App\Repository;

use App\Entity\Rules;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Rules>
 */
class RulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rules::class);
    }

    public function getLastPublishedRules(): Rules
    {
        return $this->createQueryBuilder("r")
            ->where("r.publishedAt <= NOW()")
            ->setMaxResults(1)
            ->orderBy("r.publishedAt", "DESC")
            ->getQuery()
            ->getSingleResult();
    }
}
