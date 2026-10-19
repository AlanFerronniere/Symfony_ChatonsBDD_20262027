<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Chaton>
     */
    #[ORM\OneToMany(targetEntity: Chaton::class, mappedBy: 'categorie', orphanRemoval: true)]
    private Collection $chatons;

    public function __construct()
    {
        $this->chatons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Chaton>
     */
    public function getChatons(): Collection
    {
        return $this->chatons;
    }

    public function addChaton(Chaton $chaton): static
    {
        if (!$this->chatons->contains($chaton)) {
            $this->chatons->add($chaton);
            $chaton->setCategorie($this);
        }

        return $this;
    }

    public function removeChaton(Chaton $chaton): static
    {
        if ($this->chatons->removeElement($chaton)) {
            // set the owning side to null (unless already changed)
            if ($chaton->getCategorie() === $this) {
                $chaton->setCategorie(null);
            }
        }

        return $this;
    }
}
