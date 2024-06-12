<?php

namespace App\Repository;

use App\Entity\HelpCenterFaq;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<HelpCenterFaq>
 */
class HelpCenterFaqRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, HelpCenterFaq::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('f')
            ->orderBy('f.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('f.updatedAt <= :now')
            ->orWhere('f.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::HELPCENTERFAQ_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['f.id', 'f.name'],
            ]
        );
    }

    /**
     * Retrieves questions/answers randomly.
     * @return HelpCenterFaq[]
     */
    public function findRand(int $maxResults): array // HelpCenterController
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.createdAt', 'DESC')
            ->where('f.isOnline = true')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retrieves all questions/answers.
     * @return HelpCenterFaq[]
     */
    public function findAlls() // HelpCenterController
    {
        return $this->createQueryBuilder('f')
            ->where('f.isOnline = true')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the help center faqs after applying the specified search criterias.
     *
     * @param string               $selecttags
     * @param bool                 $isOnline
     * @param string               $keyword
     * @param int                  $id
     * @param int                  $limit
     * @param string               $sort
     * @param string               $order
     * @param string               $otherthan
     *
     * @return QueryBuilder<HelpCenterFaq>
     */
    public function getHelpCenterFaqs($selecttags, $isOnline, $keyword, $id, $limit, $sort, $order, $otherthan): QueryBuilder
    {
        $qb = $this->createQueryBuilder('f');

        if (!$selecttags) {
            $qb->select('f');

            if ('all' !== $isOnline) {
                $qb->andWhere('f.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
            }

            if ('all' !== $keyword) {
                $qb->andWhere('f.question LIKE :keyword or :keyword LIKE f.question or :keyword LIKE f.answer or f.answer LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
            }

            if ('all' !== $id) {
                $qb->andWhere('f.id = :id')->setParameter('id', $id);
            }

            if ('all' !== $limit) {
                $qb->setMaxResults($limit);
            }

            if ('all' !== $otherthan) {
                $qb->andWhere('f.id != :otherthan')->setParameter('otherthan', $otherthan);
                $qb->andWhere('f.id = :otherthan')->setParameter('otherthan', $otherthan);
            }

            $qb->orderBy('f.'.$sort, $order);
        } else {
            $qb->select("SUBSTRING_INDEX(GROUP_CONCAT(f.tags SEPARATOR ','), ',', 8)");
        }

        return $qb;
    }
}
