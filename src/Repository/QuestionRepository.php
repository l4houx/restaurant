<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Retrieves questions/answers randomly.
     * @return Question[]
     */
    public function findRand(int $maxResults): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('Rand()')
            ->where('q.isOnline = true')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retrieves questions/answers.
     * @return Question[]
     */
    public function findResults(int $maxResults): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.createdAt', 'DESC')
            ->where('q.isOnline = true')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retrieves all questions/answers.
     * @return Question[]
     */
    public function findAlls()
    {
        return $this->createQueryBuilder('q')
            ->where('q.isOnline = true')
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
