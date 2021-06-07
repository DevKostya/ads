<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank(message="Марка авто должна быть указана")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Марка авто должна быть не менее 3 символов",
     *     max="128",
     *     maxMessage="Марка авто должна быть не более 128 символов",
     * )
     */
    private $brand;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank(message="Год авто должен быть указан")
     * @Assert\Type(type="integer", message="Год авто должен быть числом")
     * @Assert\GreaterThanOrEqual(value="1886", message="Год авто должен быть от 1886 до текущего года включительно")
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank(message="Производитель авто должен быть указан")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Производитель авто должен быть не менее 3 символов",
     *     max="128",
     *     maxMessage="Производитель авто должен быть не более 128 символов",
     * )
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(message="Цвет авто должен быть указан")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Цвет авто должен быть не менее 3 символов",
     *     max="32",
     *     maxMessage="Цвет авто должен быть не более 32 символов",
     * )
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Пробег авто не указан")
     * @Assert\Type(type="integer", message="Пробег авто должена быть числом")
     * @Assert\GreaterThanOrEqual(value="0", message="Пробег авто должна быть не менее 0")
     * @Assert\LessThan(value="10000000", message="Пробег авто должен быть меньше 10000000")
     */
    private $mileage;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Стоимость авто не указана")
     * @Assert\Type(type="integer", message="Стоимость авто должена быть числом")
     * @Assert\GreaterThan(value="0", message="Стоимость авто должна быть больше 0")
     * @Assert\LessThan(value="10000000", message="Стоимость авто должна быть меньше 10000000")
     */
    private $cost;

    /**
     * @Assert\NotBlank(message="Аватар авто не указан")
     * @Assert\Image(
     *     minWidth="100",
     *     minWidthMessage="Размер изображения должен быть от 100x100 до 1280×1280",
     *     minHeight="100",
     *     minHeightMessage="Размер изображения должен быть от 100x100 до 1280×1280",
     *     maxHeight="1280",
     *     maxHeightMessage="Размер изображения должен быть от 100x100 до 1280×1280",
     *     maxWidth="1280",
     *     maxWidthMessage="Размер изображения должен быть от 100x100 до 1280×1280",
     *     mimeTypes="image/jpeg",
     *     mimeTypesMessage="Неверный формат изображения"
     * )
     */
    private $avatar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): ?int
    {
        $this->id = $id;

        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $year = (int)$year;

        $this->year = $year <= date('Y') ? $year : 0;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(string $mileage): self
    {
        $this->mileage = (int)$mileage;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(string $cost): self
    {
        $this->cost = (int)$cost;

        return $this;
    }

    public function setAvatar(UploadedFile $file = null)
    {
        $this->avatar = $file;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }
}
