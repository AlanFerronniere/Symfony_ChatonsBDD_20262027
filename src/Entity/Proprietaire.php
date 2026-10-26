<?php

namespace App\Entity;

use App\Repository\ProprietaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProprietaireRepository::class)]
class Proprietaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    /**
     * @var Collection<int, Chaton>
     */
    #[ORM\ManyToMany(targetEntity: Chaton::class, mappedBy: 'proprietaires')]
    private Collection $chatons;

    public function __construct()
    {
        $this->chatons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

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
            $chaton->addProprietaire($this);
        }

        return $this;
    }

    public function removeChaton(Chaton $chaton): static
    {
        if ($this->chatons->removeElement($chaton)) {
            $chaton->removeProprietaire($this);
        }

        return $this;
    }

    //j'ajoute nomComplet pour afficher le nom complet du propriétaire
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
