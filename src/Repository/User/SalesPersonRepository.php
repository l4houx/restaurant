<?php

namespace App\Repository\User;

use App\Entity\User\Manager;
use App\Entity\Company\Member;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User\SalesPerson;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SalesPerson>
 */
class SalesPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesPerson::class);
    }

    public function createQueryBuilderSalesPersonsByManager(Manager $manager): QueryBuilder
    {
        $qb = $this->createQueryBuilder("s")
            ->addSelect("m")
            ->join("s.member", "m")
            ->orderBy("s.firstname", "asc")
            ->addOrderBy("s.lastname", "asc")
        ;

        $qb->andWhere(
            $qb->expr()->in(
                "m.id",
                $manager->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
            )
        );

        return $qb;
    }

    //    /**
    //     * @return SalesPerson[] Returns an array of SalesPerson objects
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

    //    public function findOneBySomeField($value): ?SalesPerson
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
