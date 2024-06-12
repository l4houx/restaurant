<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Currency>
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * Returns the currencies after applying the specified search criterias.
     *
     * @param string      $ccy
     * @param string|null $symbol
     *
     * @return QueryBuilder
     */
    public function getCurrencies($ccy, $symbol): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c');

        if ('all' !== $ccy) {
            $qb->andWhere('c.ccy = :ccy')->setParameter('ccy', $ccy);
        }

        if ('all' !== $symbol) {
            $qb->andWhere('c.symbol = :symbol')->setParameter('symbol', $symbol);
        }

        return $qb;
    }
}
