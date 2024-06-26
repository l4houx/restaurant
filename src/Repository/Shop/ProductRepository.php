<?php

namespace App\Repository\Shop;

use App\Entity\User;
use App\Entity\Shop\Filter;
use App\Entity\Shop\Product;
use App\Entity\Shop\Category;
use Doctrine\ORM\QueryBuilder;
use App\Entity\HomepageHeroSetting;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Paginator<Product>
     */
    public function getPaginatedFavorites(int $page, int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.isOnline = true')

            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        return new Paginator($qb);
    }

    /**
     * @return Paginator<Product>
     */
    public function getPaginated(
        int $page,
        int $limit,
        string $sort,
        ?Category $category,
        Filter $filter
    ): Paginator {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->andWhere('p.amount >= :min')
            ->setParameter('min', $filter->min)
            ->andWhere('p.amount <= :max')
            ->setParameter('max', $filter->max)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        /*if ($category !== null) {
            $qb
                ->andWhere("c.left >= :left")
                ->andWhere("c.right <= :right")
                ->setParameter("left", $category->getLeft())
                ->setParameter("right", $category->getRight())
            ;
        }*/

        if (null !== $filter->brand) {
            $qb
                ->andWhere('b = :brand')
                ->setParameter('brand', $filter->brand)
            ;
        }

        if (null !== $filter->keywords) {
            $qb
                ->andWhere("CONCAT(p.name, ' ', p.content, ' ', b.name) LIKE :keywords")
                ->setParameter('keywords', $filter->keywords)
            ;
        }

        switch ($sort) {
            case 'amount-asc':
                $qb->orderBy('p.amount', 'asc');
                break;
            case 'amount-desc':
                $qb->orderBy('p.amount', 'desc');
                break;
            case 'name-asc':
                $qb->orderBy('p.name', 'asc');
                break;
            case 'name-desc':
                $qb->orderBy('p.name', 'desc');
                break;
            default:
                $qb->orderBy('lp.id', 'desc')->orderBy('p.id', 'desc');
                break;
        }

        return new Paginator($qb);
    }

    /**
     * @return array<Product>
     */
    public function getLastProducts(int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('c')
            ->join('p.brand', 'b')
            ->join('p.category', 'c')
            ->leftJoin('c.lastProduct', 'lp')
            ->setMaxResults($limit)
            ->orderBy('lp.id', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMinAmount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.amount')
            ->where('p.isOnline = true')
            ->orderBy('p.amount', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getMaxAmount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.amount')
            ->where('p.isOnline = true')
            ->orderBy('p.amount', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Returns the products after applying the specified search criterias.
     *
     * @param string               $selecttags
     * @param bool                 $isOnline
     * @param string               $keyword
     * @param int                  $id
     * @param string               $slug
     * @param Collection           $addedtofavoritesby
     * @param ?HomepageHeroSetting $isOnHomepageSlider
     * @param Collection           $subCategories
     * @param string               $ref
     * @param int                  $limit
     * @param string               $sort
     * @param string               $order
     * @param string               $otherthan
     *
     * @return QueryBuilder<Product>
     */
    public function getProducts($selecttags, $isOnline, $keyword, $id, $slug, $addedtofavoritesby, $isOnHomepageSlider, $subCategories, $ref, $limit, $sort, $order, $otherthan, $count): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if (!$selecttags) {
            if ($count) {
                $qb->select('COUNT(DISTINCT p)');
            } else {
                $qb->select('DISTINCT p');
            }

            if ('all' !== $isOnline) {
                $qb->andWhere('p.isOnline = :isOnline')->setParameter('isOnline', $isOnline);
            }

            if ('all' !== $keyword) {
                $qb->andWhere('p.name LIKE :keyword or :keyword LIKE p.name or :keyword LIKE p.content or p.content LIKE :keyword or :keyword LIKE p.tags or p.tags LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
            }

            if ('all' !== $id) {
                $qb->andWhere('p.id = :id')->setParameter('id', $id);
            }

            if ('all' !== $slug) {
                $qb->andWhere('p.slug = :slug')->setParameter('slug', $slug);
            }

            if ('all' !== $addedtofavoritesby) {
                $qb->andWhere(':addedtofavoritesbyuser MEMBER OF p.addedtofavoritesby')->setParameter('addedtofavoritesbyuser', $addedtofavoritesby);
            }

            if (true === $isOnHomepageSlider) {
                $qb->andWhere('p.isonhomepageslider IS NOT NULL');
            }

            if ('all' !== $subCategories) {
                $qb->leftJoin('p.subCategories', 'subCategories');
                $qb->andWhere('subCategories.id = :subCategories')->setParameter('subCategories', $subCategories);
            }

            if ('all' !== $ref) {
                $qb->andWhere('p.ref = :ref')->setParameter('ref', $ref);
            }

            if ('all' !== $limit) {
                $qb->setMaxResults($limit);
            }

            if ('all' !== $otherthan) {
                $qb->andWhere('p.id != :otherthan')->setParameter('otherthan', $otherthan);
                $qb->andWhere('p.id = :otherthan')->setParameter('otherthan', $otherthan);
            }

            $qb->orderBy('p.'.$sort, $order);
        } else {
            $qb->select("SUBSTRING_INDEX(GROUP_CONCAT(p.tags SEPARATOR ','), ',', 8)");
        }

        return $qb;
    }
}
