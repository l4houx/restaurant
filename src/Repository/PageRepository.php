<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Page::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.updatedAt <= :now')
            //->orWhere('p.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::PAGE_LIMIT,
            ['wrap-queries' => true],
            [
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name'],
            ]
        );
    }

    public function getPages(string $slug): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p');

        if ('all' !== $slug) {
            $qb->andWhere('p.slug = :slug')->setParameter('slug', $slug);
        }

        return $qb;
    }
}
