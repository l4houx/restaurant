<?php

namespace App\Repository\Shop;

use App\Entity\Traits\HasLimit;
use App\Entity\Shop\SubCategory;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SubCategory>
 */
class SubCategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, SubCategory::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            //->setParameter('now', new \DateTimeImmutable())
            //->where('s.updatedAt <= :now')
            //->orWhere('s.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::SUBCATEGORY_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['s.id', 's.name'],
            ]
        );
    }

    /**
     * @return array<SubCategory>
     */
    public function getSubCategories(): array
    {
        return $this->createQueryBuilder("s")
            ->addSelect("c")
            ->addSelect("ch")
            ->join("s.categories", "c")
            ->leftJoin("c.children", "ch")
            ->orderBy("s.name", "asc")
            ->getQuery()
            ->getResult()
        ;
    }
}
