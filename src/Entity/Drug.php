<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DrugRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DrugRepository::class)
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "put", "delete"},
 *     normalizationContext={"groups"={"drugs:read"}},
 *     denormalizationContext={"groups"={"drugs:write"}}
 * )
 */
class Drug
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"drugs:read", "drugs:write"})
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Substance::class, inversedBy="drugs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"drugs:read", "drugs:write"})
     */
    private $substance;

    /**
     * @ORM\ManyToOne(targetEntity=Manufacturer::class, inversedBy="drugs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"drugs:read", "drugs:write"})
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * @Groups({"drugs:read", "drugs:write"})
     */
    private $price;

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
