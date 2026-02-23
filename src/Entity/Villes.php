<?php

namespace App\Entity;

use App\Repository\VillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VillesRepository::class)]
class Villes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idVille = null;

    #[ORM\Column(length: 30)]
    private ?string $nom_ville = null;

    #[ORM\Column(length: 10)]
    private ?string $cpo = null;

    /**
     * @var Collection<int, Lieux>
     */
    #[ORM\OneToMany(targetEntity: Lieux::class, mappedBy: 'noVilles')]
    private Collection $lieuxes;

    public function __construct()
    {
        $this->lieuxes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdVille(): ?int
    {
        return $this->idVille;
    }

    public function setIdVille(int $idVille): static
    {
        $this->idVille = $idVille;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->nom_ville;
    }

    public function setNomVille(string $nom_ville): static
    {
        $this->nom_ville = $nom_ville;

        return $this;
    }

    public function getCpo(): ?string
    {
        return $this->cpo;
    }

    public function setCpo(string $cpo): static
    {
        $this->cpo = $cpo;

        return $this;
    }

    /**
     * @return Collection<int, Lieux>
     */
    public function getLieuxes(): Collection
    {
        return $this->lieuxes;
    }

    public function addLieux(Lieux $lieux): static
    {
        if (!$this->lieuxes->contains($lieux)) {
            $this->lieuxes->add($lieux);
            $lieux->setNoVilles($this);
        }

        return $this;
    }

    public function removeLieux(Lieux $lieux): static
    {
        if ($this->lieuxes->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getNoVilles() === $this) {
                $lieux->setNoVilles(null);
            }
        }

        return $this;
    }
}
