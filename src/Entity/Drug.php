<?php

namespace App\Entity;

use App\Repository\DrugRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DrugRepository::class)
 * @OA\Schema(
 *     title="Лекарство",
 *     description="Модель лекарства",
 *     required={"name", "price", "substance", "manufacturer"},
 *     @OA\Xml(
 *         name="Drug"
 * 	   )
 * )
 */
class Drug
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"drugs:read"})
	 * @OA\Property(
	 *     type="integer",
	 *     description="ID",
	 *     title="ID"
	 * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Name must not be blank")
     * @Groups({"drugs:read"})
	 * @OA\Property(
	 *     type="string",
	 *     description="Название лекарства",
	 *     title="Название лекарственного препарата"
	 * )
     */
    private $name;

	/**
	 * @ORM\Column(type="float")
	 * @Assert\NotBlank(message="Price must not be blank")
	 * @Assert\GreaterThan(0)
	 * @Groups({"drugs:read"})
	 * @OA\Property(
	 *     type="number",
	 *     format="float",
	 *     description="Цена лекарства",
	 *     title="Цена лекарственного препарата"
	 * )
	 */
	private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Substance::class, inversedBy="drugs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"drugs:read"})
	 * @Assert\NotBlank(message="Substance must not be blank")
	 * @OA\Property(
	 *     title="Вещество",
	 *     description="Действующее вещество в лекарственном препарате"
	 * )
	 * @var Substance
     */
    private $substance;

    /**
     * @ORM\ManyToOne(targetEntity=Manufacturer::class, inversedBy="drugs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"drugs:read"})
	 * @Assert\NotBlank(message="Manufacturer must not be blank")
	 * @OA\Property(
	 *     title="Производитель",
	 *     description="Производитель лекарственного препарата"
	 * )
	 * @var Manufacturer
     */
    private $manufacturer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSubstance(): ?Substance
    {
        return $this->substance;
    }

    public function setSubstance(?Substance $substance): self
    {
        $this->substance = $substance;

        return $this;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
