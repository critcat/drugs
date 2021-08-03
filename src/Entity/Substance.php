<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SubstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubstanceRepository::class)
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"={
 *          "controller"=NotFoundAction::class,
 *          "read"=false,
 *          "output"=false
 *     }},
 *     normalizationContext={"groups"={"substance:read"}},
 *     order={"name"="ASC"},
 * )
 */
class Substance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"substance:list", "substance:read", "drugs:read"})
     * @Assert\NotNull()
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
