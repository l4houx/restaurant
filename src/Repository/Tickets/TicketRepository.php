<?php

namespace App\Repository\Tickets;

use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * @return Ticket[]
     */
    public function findAlls()
    {
        return $this->createQueryBuilder('t')
            // ->where('t.isOnline = true')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find For Pagination Tickets
     *
     * @param int $page
     * @param ?int $userId
     * 
     * @return PaginationInterface
     */
    public function findForPagination(int $page, ?int $userId): PaginationInterface
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.status', 's')
            ->leftJoin('t.level', 'l')
            ->select('t', 's', 'l')
            ->where('t.createdAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.createdAt', 'DESC')
        ;

        if ($userId) {
            $qb = $qb->andWhere('t.user = :user')->setParameter('user', $userId);
        }

        $qb->getQuery()->getResult();
        $qb = $this->paginator->paginate($qb, $page, HasLimit::TICKET_LIMIT, ['wrap-queries' => true]);

        return $qb;
    }

    /**
     * Get Paginated Tickets
     *
     * @param int $page
     * @param int $limit
     * @param ?int $userId
     * 
     * @return Paginator<Ticket>
     */
    public function getPaginated(int $page, int $limit, ?int $userId): Paginator
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.status', 's')
            ->leftJoin('t.level', 'l')
            ->select('t', 's', 'l')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->where('t.createdAt <= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('t.createdAt', 'DESC')
        ;

        if ($userId) {
            $qb = $qb->andWhere('t.user = :user')->setParameter('user', $userId);
        }

        $qb->getQuery()->getResult();

        return new Paginator($qb);
    }
}
