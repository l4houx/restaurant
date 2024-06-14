<?php

namespace App\Repository\Company;

use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Entity\Traits\HasLimit;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Client::class);
    }

    public function findForPagination(
        Manager $employee,
        int $page,
        int $limit,
        ?string $keywords
    ): PaginationInterface {
        $builder = $this->createQueryBuilder('c')
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

        $builder->andWhere(
            $builder->expr()->in(
                'm.id',
                $employee->getMembers()->map(fn (Member $member) => $member->getId())->toArray()
            )
        );

        // return new Paginator($builder);

        return $this->paginator->paginate(
            $builder,
            $page,
            $limit
        );
    }

    public function createQueryBuilderClientsByEmployee(SalesPerson|Manager $employee): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('m')
            ->join('c.member', 'm')
            ->orderBy('c.name', 'asc')
        ;

        if ($employee instanceof Manager) {
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
