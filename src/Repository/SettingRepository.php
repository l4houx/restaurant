<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function findAllForTwig(): array
    {
        return $this->createQueryBuilder('s', 's.name')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getValue(string $name): mixed
    {
        try {
            return $this->createQueryBuilder('s')
                ->select('s.value')
                ->where('s.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }

    public function getIndexQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->where('s.type IS NOT NULL')
            ->orderBy('s.label')
        ;
    }
}
