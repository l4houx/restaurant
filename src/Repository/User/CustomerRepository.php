<?php

namespace App\Repository\User;

use App\Entity\User\Manager;
use App\Entity\User\Customer;
use App\Entity\Company\Member;
use App\Entity\Traits\HasLimit;
use App\Entity\User\SalesPerson;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Customer::class);
    }

    public function findForPagination(
        Manager | SalesPerson $employee,
        int $page,
        int $limit,
        ?string $keywords
    ): PaginationInterface {
        $builder = $this->createQueryBuilder("u")
            ->addSelect("c")
            ->addSelect("m")
            ->join("u.client", "c")
            ->join("c.member", "m")
            ->andWhere("CONCAT(u.firstname, ' ', u.lastname, ' ', c.name) LIKE :keywords")
            ->setParameter("keywords", "%" . ($keywords ?? "") . "%")
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy("u.firstname", "asc")
            ->addOrderBy("u.lastname", "asc")
        ;

        if ($employee instanceof SalesPerson) {
            $builder
                ->andWhere("c.salesPerson = :salesPerson")
                ->setParameter("salesPerson", $employee)
            ;
        } else {
            $builder->andWhere(
                $builder->expr()->in(
                    "m.id",
                    $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
                )
            );
        }

        //return new Paginator($builder);

        return $this->paginator->paginate(
            $builder,
            $page,
            $limit
        );
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
