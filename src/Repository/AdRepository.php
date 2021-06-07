<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * Получить объявления с авто
     * @return array
     */
    public function getAds(array $filters) : array
    {
        $query = $this->createQueryBuilder('ad')
            ->select('ad.id', 'ad.ad_uuid', 'car.id as car_id', 'car.brand',
                'car.year', 'car.manufacturer', 'car.color', 'car.mileage', 'car.cost');
        $query = $this->filterBrand($query, $filters);
        $query = $this->filterManufacturer($query, $filters);
        $query = $this->filterColor($query, $filters);
        $query = $this->filterYear($query, $filters);
        $query = $this->filterMileageFrom($query, $filters);
        $query = $this->filterMileageTo($query, $filters);
        $query = $this->filterCostFrom($query, $filters);
        $query = $this->filterCostTo($query, $filters);
        $query = $query->innerJoin('ad.car', 'car')
            ->getQuery();
        return $query->execute();
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterBrand(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['brand']) && $filters['brand']) {
            $query = $query->andWhere('car.brand = :brand')
                ->setParameter('brand', $filters['brand']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterManufacturer(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['manufacturer']) && $filters['manufacturer']) {
            $query = $query->andWhere('car.manufacturer = :manufacturer')
                ->setParameter('manufacturer', $filters['manufacturer']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterColor(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['color']) && $filters['color']) {
            $query = $query->andWhere('car.color = :color')
                ->setParameter('color', $filters['color']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterYear(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['year']) && $filters['year']) {
            $query = $query->andWhere('car.year = :year')
                ->setParameter('year', $filters['year']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterMileageFrom(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['mileage_from']) && $filters['mileage_from']) {
            $query = $query->andWhere('car.mileage >= :mileage_from')
                ->setParameter('mileage_from', $filters['mileage_from']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterMileageTo(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['mileage_to']) && $filters['mileage_to'] != 10000000) {
            $query = $query->andWhere('car.mileage <= :mileage_to')
                ->setParameter('mileage_to', $filters['mileage_to']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterCostFrom(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['cost_from']) && $filters['cost_from']) {
            $query = $query->andWhere('car.cost >= :cost_from')
                ->setParameter('cost_from', $filters['cost_from']);
        }
        return $query;
    }

    /**
     * @param  QueryBuilder  $query
     * @param  array  $filters
     * @return QueryBuilder
     */
    public function filterCostTo(QueryBuilder $query, array $filters) : QueryBuilder
    {
        if (isset($filters['cost_to']) && $filters['cost_to'] != 10000000) {
            $query = $query->andWhere('car.cost <= :cost_to')
                ->setParameter('cost_to', $filters['cost_to']);
        }
        return $query;
    }
}
