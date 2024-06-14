<?php

namespace App\Repository\Shop;

use App\Entity\Shop\SubCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

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

    /**
     * @return array<SubCategory>
     */
    public function getSubCategories(): array
    {
        return $this->createQueryBuilder('u')
            ->addSelect('c')
            ->addSelect('ch')
            ->join('u.categories', 'c')
            ->leftJoin('c.children', 'ch')
            ->orderBy('u.name', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }
}
