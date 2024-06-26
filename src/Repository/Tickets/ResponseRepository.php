<?php

namespace App\Repository\Tickets;

use App\Entity\Tickets\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Response>
 */
class ResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Response::class);
    }

    /**
     * @return Response[]
     */
    public function findAlls()
    {
        return $this->createQueryBuilder('r')
            //->where('r.isOnline = true')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
