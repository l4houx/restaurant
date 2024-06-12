<?php

namespace App\Repository\User;

use App\Entity\User\SuperAdministrator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuperAdministrator>
 */
class SuperAdministratorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuperAdministrator::class);
    }

    /**
     * Query used to retrieve a user for the login.
     */
    public function findUserByEmailOrUsername(string $usernameOrEmail): ?SuperAdministrator
    {
        return $this->createQueryBuilder('s')
            ->where('LOWER(s.email) = :identifier')
            // ->where('LOWER(s.email) = :identifier OR LOWER(s.username) = :identifier')
            // ->andWhere('s.isVerified = true')
            ->orWhere('LOWER(u.username) = :identifier')
            ->setParameter('identifier', mb_strtolower($usernameOrEmail))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return SuperAdministrator[] Returns an array of SuperAdministrator objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SuperAdministrator
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
