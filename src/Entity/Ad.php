<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=AdRepository::class)
 */
class Ad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $ad_uuid;

    /**
     * @ORM\OneToOne(targetEntity=Car::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $car;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdUuid(): ?string
    {
        return $this->ad_uuid;
    }

    public function setAdUuid(string $ad_uuid = null): self
    {
        if (!$ad_uuid || !Uuid::isValid($ad_uuid)) {
            $ad_uuid = Uuid::v4();
        }
        $this->ad_uuid = $ad_uuid;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    public function getAdParams(): array
    {
        $car = $this->getCar();
        return [
            'ad_uuid' => $this->getAdUuid(),
            'brand' => $car->getBrand(),
            'cost' => $car->getCost(),
            'color' => $car->getColor(),
            'manufacturer' => $car->getManufacturer(),
            'mileage' => $car->getMileage(),
            'year' => $car->getYear(),
            'avatar' => '/uploads/' . $car->getId() . '/avatar.jpeg'
        ];
    }
}
