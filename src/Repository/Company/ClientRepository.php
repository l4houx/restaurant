<?php

namespace App\Repository\Company;

use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

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
     * @return Paginator<Client>
     */
    public function getPaginated(
        Manager|SuperAdministrator $employee,
        int $page,
        int $limit,
        ?string $keywords
    ): Paginator {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('s')
            ->addSelect('m')
            ->leftJoin('c.salesPerson', 's')
            ->join('c.member', 'm')
            ->andWhere('c.name LIKE :keywords')
            ->setParameter('keywords', '%'.($keywords ?? '').'%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('c.name', 'asc')
        ;

        $qb->andWhere(
            $qb->expr()->in(
                'm.id',
                $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
            )
        );

        return new Paginator($qb);
    }

    /**
     * @param SalesPerson|Manager|SuperAdministrator $employee
     */
    public function createQueryBuilderClientsByEmployee(SalesPerson|Manager|SuperAdministrator $employee): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('m')
            ->join('c.member', 'm')
            ->orderBy('c.name', 'asc')
        ;

        if ($employee instanceof Manager && $employee instanceof SuperAdministrator) {
            $qb->andWhere(
                $qb->expr()->in(
                    'm.id',
                    $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                )
            );
        } else {
            $qb->where('m = :member')->setParameter('member', $employee->getMember());
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
