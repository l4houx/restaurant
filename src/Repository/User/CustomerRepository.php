<?php

namespace App\Repository\User;

use App\Entity\Company\Member;
use App\Entity\User\Customer;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * @return Paginator<Customer>
     */
    public function getPaginated(
        Manager|SalesPerson|SuperAdministrator $employee,
        int $page,
        int $limit,
        ?string $keywords
    ): Paginator {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('c')
            ->addSelect('m')
            ->join('u.client', 'c')
            ->join('c.member', 'm')
            ->andWhere("CONCAT(u.firstname, ' ', u.lastname, ' ', c.name) LIKE :keywords")
            ->setParameter('keywords', '%'.($keywords ?? '').'%')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('u.firstname', 'asc')
            ->addOrderBy('u.lastname', 'asc')
        ;

        if ($employee instanceof SalesPerson) {
            $qb
                ->andWhere('c.salesPerson = :salesPerson')
                ->setParameter('salesPerson', $employee)
            ;
        } else {
            $qb->andWhere(
                $qb->expr()->in(
                    'm.id',
                    $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                )
            );
        }

        return new Paginator($qb);
    }

    //    /**
    //     * @return Customer[] Returns an array of Customer objects
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

    //    public function findOneBySomeField($value): ?Customer
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
