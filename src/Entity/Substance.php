<?php

namespace App\Entity;

use App\Repository\SubstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubstanceRepository::class)
 * @OA\Schema(
 *     title="Действующее вещество",
 *     description="Модель действующего вещества",
 *     required={"name"},
 *     @OA\Xml(
 *         name="Substance"
 * 	   )
 * )
 */
class Substance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"substance:read", "drugs:read"})
	 * @OA\Property(
	 *     type="integer",
	 *     description="ID",
	 *     title="ID"
	 * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"substance:list", "drugs:read"})
     * @Assert\NotBlank(message="Name must not be blank")
	 * @OA\Property(
	 *     type="string",
	 *     description="Название действующего вещества",
	 *     title="Название вещества"
	 * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Drug::class, mappedBy="substance")
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
            $drug->setSubstance($this);
        }

        return $this;
    }

    public function removeDrug(Drug $drug): self
    {
        if ($this->drugs->removeElement($drug)) {
            // set the owning side to null (unless already changed)
            if ($drug->getSubstance() === $this) {
                $drug->setSubstance(null);
            }
        }

        return $this;
    }
}
