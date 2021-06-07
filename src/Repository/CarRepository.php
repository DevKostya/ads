<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Car|null find($id, $lockMode = null, $lockVersion = null)
 * @method Car|null findOneBy(array $criteria, array $orderBy = null)
 * @method Car[]    findAll()
 * @method Car[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Car::class);
    }

    /**
     * Получить все бренды автомобиля в системе
     * @return array
     */
    public function getCarsBrands() : array
    {
        $query = $this->createQueryBuilder('car')
            ->select('car.brand')
            ->distinct(true)
            ->getQuery();
        return $query->getResult();
    }

    /**
     * Получить всех призводителей авто
     * @return array
     */
    public function getCarsManufacturer() : array
    {
        $query = $this->createQueryBuilder('car')
            ->select('car.manufacturer')
            ->distinct(true)
            ->getQuery();
        return $query->getResult();
    }

    /**
     * Получить все цвета авто
     * @return array
     */
    public function getCarsColor() : array
    {
        $query = $this->createQueryBuilder('car')
            ->select('car.color')
            ->distinct(true)
            ->getQuery();
        return $query->getResult();
    }

    /**
     * @param  Car  $car
     * @return Ad
     */
    public function createWithAd(Car $car): Ad
    {
        $ad = new Ad();
        $ad->setAdUuid();
        $this->entityManager->transactional(function($em) use ($car, &$ad) {
            $em->persist($car);
            $ad->setCar($car);
            $em->persist($ad);
            $em->flush();
        });
        return $ad;
    }

    /**
     * @param  Car  $car
     * @return void
     */
    public function update(Car $car): void
    {
        $this->entityManager->transactional(function($em) use ($car) {
            $em->merge($car);
            $em->flush();
        });
    }

    /**
     * @param  Ad  $ad
     * @return void
     */
    public function deleteWithAd(Ad $ad): void
    {
        $this->entityManager->transactional(function($em) use ($ad) {
            $em->remove($ad->getCar());
            $em->remove($ad);
            $em->flush();
        });
    }
}
