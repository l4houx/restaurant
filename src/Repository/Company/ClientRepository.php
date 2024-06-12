<?php

namespace App\Repository\Company;

use App\Entity\User\Manager;
use App\Entity\Company\Client;
use App\Entity\Company\Member;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User\SalesPerson;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param SalesPerson|Manager $employee
     */
    public function createQueryBuilderClientsByEmployee(SalesPerson | Manager $employee): QueryBuilder
    {
        $qb = $this->createQueryBuilder("c")
            ->addSelect("m")
            ->join("c.member", "m")
            ->orderBy("c.name", "asc")
        ;

        if ($employee instanceof Manager) {
            $qb->andWhere(
                $qb->expr()->in(
                    "m.id",
                    $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                )
            );
        } else {
            $qb->where("m = :member")->setParameter("member", $employee->getMember());
        }

        return $qb;
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
