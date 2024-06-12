<?php

namespace App\Repository\Shop;

use App\Entity\Filter;
use App\Entity\Shop\Product;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Traits\HasLimit;
use App\Entity\Shop\SubCategory;
use App\Entity\HomepageHeroSetting;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Product::class);
    }

    public function findForPagination(int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('s')
            ->join('p.brand', 'b')
            ->join('p.subCategories', 's')
            ->orderBy('p.updatedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->where('p.updatedAt <= :now')
            ->orWhere('p.isOnline = true')
        ;

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::PRODUCT_LIMIT,
            [
                'wrap-queries' => true,
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name'],
            ]
        );
    }

    public function findForUserPagination(int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')->leftJoin('p.subCategories', 'c')->select('p', 's');

        if ($userId) {
            $builder = $builder->andWhere('p.author = :user')->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page,
            HasLimit::PRODUCT_LIMIT,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['p.id', 'p.name', 'p.subCategories'],
            ]
        );
    }

    public function getForPagination(
        int $page,
        int $limit,
        string $sort,
        ?SubCategory $subCategory,
        Filter $filter
    ): PaginationInterface {
        $builder = $this->createQueryBuilder('p')
            ->addSelect('b')
            ->addSelect('s')
            ->join('p.brand', 'b')
            ->join('p.subCategories', 's')
            ->andWhere('p.isOnline = true')
            ->andWhere('p.price >= :min')
            ->setParameter('min', $filter->min)
            ->andWhere('p.price <= :max')
            ->setParameter('max', $filter->max)
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
        ;

        $this->filterProducts($builder, $subCategory, $filter);

        switch ($sort) {
            case 'price-asc':
                $builder->orderBy('p.price', 'asc');
                break;
            case 'price-desc':
                $builder->orderBy('p.price', 'desc');
                break;
            case 'name-asc':
                $builder->orderBy('p.name', 'asc');
                break;
            case 'name-desc':
                $builder->orderBy('p.name', 'desc');
                break;
            default:
                $builder->orderBy('s.id', 'desc')->orderBy('p.id', 'desc');
                break;
        }

        $products = $this->paginator->paginate($builder, $page);

        return $products;
    }

    private function filterProducts(
        QueryBuilder $builder,
        ?SubCategory $subCategory,
        Filter $filter
    ): void {
        if (null !== $filter->brand) {
            $builder
                ->andWhere('b = :brand')
                ->setParameter('brand', $filter->brand)
            ;
        }

        if (null !== $filter->keywords) {
            $builder
                ->andWhere("CONCAT(p.name, ' ', p.content, ' ', b.name) LIKE :keywords")
                ->setParameter('keywords', '%'.$filter->keywords.'%')
            ;
        }
    }

    public function getMinPrice(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.price')
            ->where('p.isOnline = true')
            ->orderBy('p.price', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getMaxPrice(): int
    {
        return $this->createQueryBuilder('p')
            ->select('p.price')
            ->where('p.isOnline = true')
            ->orderBy('p.price', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findByKeyword(string $keyword): array
    {
        $qb = $this
            ->createQueryBuilder('p');

        $qb
            ->select('p.id', 'p.content')
            ->where($qb->expr()->like('p.id', ':keyword'))
            ->orWhere($qb->expr()->like('p.content', ':keyword'))
            ->setParameter('keyword', '%'.$keyword.'%');

        return $qb
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * @return QueryBuilder<Product>
     */
    public function findRecents(int $maxResults): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.online = true AND p.createdAt < NOW()')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($maxResults)
        ;
    }

    public function findRecent(int $maxResults): array
    {
        return $this->createQueryBuilder('p')
            // ->andWhere('p.isOnline = :isOnline')
            ->andWhere('p.createdAt <= :now')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Product[] returns an array of Product objects similar with the given product
     */
    public function findSimilar(Product $product, int $maxResults = 4): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.subCategories', 's')
            ->addSelect('COUNT(s) AS HIDDEN numberOfSubCategories')
            ->andWhere('s IN (:subCategories)')
            ->andWhere('p != :product')
            //->setParameter('subCategories', $product->getSubCategories())
            ->setParameter('product', $product)
            ->groupBy('p')
            ->addOrderBy('numberOfSubCategories', 'DESC')
            ->addOrderBy('p.createdAt', 'DESC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the products after applying the specified search criterias.
     *
     * @param string               $selecttags
     * @param bool                 $isOnline
     * @param \DateTimeImmutable   $elapsed
     * @param string               $keyword
     * @param int                  $id
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
    public function getProducts($selecttags, $isOnline, $elapsed, $keyword, $id, $addedtofavoritesby, $isOnHomepageSlider, $subCategories, $ref, $limit, $sort, $order, $otherthan, $count): QueryBuilder
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

            if ('all' !== $elapsed) {
                $qb
                    ->orderBy('p.createdAt', 'DESC')
                    ->andWhere('p.createdAt <= :now')
                    ->setParameter('now', new \DateTimeImmutable())
                ;
                /*
                if (true === $elapsed || '1' == $elapsed) {
                    $qb->andWhere('p.createdAt < CURRENT_TIMESTAMP()');
                } elseif (false === $elapsed || '0' == $elapsed) {
                    $qb->andWhere('p.createdAt >= CURRENT_TIMESTAMP()');
                }
                */
            }

            if ('all' !== $keyword) {
                $qb->andWhere('p.name LIKE :keyword or :keyword LIKE p.name or :keyword LIKE p.content or p.content LIKE :keyword or :keyword LIKE p.tags or p.tags LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
            }

            if ('all' !== $id) {
                $qb->andWhere('p.id = :id')->setParameter('id', $id);
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
