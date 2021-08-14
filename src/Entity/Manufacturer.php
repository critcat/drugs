<?php

namespace App\Entity;

use App\Repository\ManufacturerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ManufacturerRepository::class)
 * @OA\Schema(
 *     title="Производитель",
 *     description="Модель производителя",
 *     required={"name", "site"},
 *     @OA\Xml(
 *         name="Manufacturer"
 * 	   )
 * )
 */
class Manufacturer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"manufacturer:read", "drugs:read"})
	 * @OA\Property(
	 *     type="integer",
	 *     description="ID",
	 *     title="ID"
	 * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"manufacturer:read", "drugs:read"})
     * @Assert\NotBlank(message="Name must not be blank")
	 * @OA\Property(
	 *     type="string",
	 *     description="Название производителя",
	 *     title="Название производителя"
	 * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"manufacturer:read", "drugs:read"})
     * @Assert\NotBlank(message="Site must not be blank")
	 * @OA\Property(
	 *     type="string",
	 *     description="Сайт производителя",
	 *     title="Сайт производителя"
	 * )
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity=Drug::class, mappedBy="manufacturer")
     */
    private $drugs;

    public function __construct()
    {
        $this->drugs = new ArrayCollection();
    }

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

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection|Drug[]
     */
    public function getDrugs(): Collection
    {
        return $this->drugs;
    }

    public function addDrug(Drug $drug): self
    {
        if (!$this->drugs->contains($drug)) {
            $this->drugs[] = $drug;
            $drug->setManufacturer($this);
        }

        return $this;
    }

    public function removeDrug(Drug $drug): self
    {
        if ($this->drugs->removeElement($drug)) {
            // set the owning side to null (unless already changed)
            if ($drug->getManufacturer() === $this) {
                $drug->setManufacturer(null);
            }
        }

        return $this;
    }
}
