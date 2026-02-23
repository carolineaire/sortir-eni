<?php

namespace App\Entity;

use App\Repository\SortiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SortiesRepository::class)]
class Sorties
{

    #[ORM\Id]
    #[ORM\Column(name: "no_sortie")]
    private ?int $idSortie = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCloture = null;

    #[ORM\Column]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $etatSortie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlPhoto = null;

    #[ORM\Column]
    private ?int $organisateur = null;

    /**
     * @var Collection<int, Inscriptions>
     */
    #[ORM\OneToMany(targetEntity: Inscriptions::class, mappedBy: 'noSorties')]
    private Collection $inscriptions;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn( name: "lieux_no_lieu", referencedColumnName: "no_lieu", nullable: false )]
    private ?Lieux $noLieux = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn( name: "etats_no_etat", referencedColumnName: "no_etat", nullable: false )]
    private ?Etats $etat = null;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSortie(): ?int
    {
        return $this->idSortie;
    }

    public function setIdSortie(int $idSortie): static
    {
        $this->idSortie = $idSortie;

        return $this;
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

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeImmutable
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeImmutable $dateCloture): static
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

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

    public function getEtatSortie(): ?int
    {
        return $this->etatSortie;
    }

    public function setEtatSortie(?int $etatSortie): static
    {
        $this->etatSortie = $etatSortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): static
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getOrganisateur(): ?int
    {
        return $this->organisateur;
    }

    public function setOrganisateur(int $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, Inscriptions>
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): static
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions->add($inscription);
            $inscription->setNoSorties($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): static
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getNoSorties() === $this) {
                $inscription->setNoSorties(null);
            }
        }

        return $this;
    }

    public function getNoLieux(): ?Lieux
    {
        return $this->noLieux;
    }

    public function setNoLieux(?Lieux $noLieux): static
    {
        $this->noLieux = $noLieux;

        return $this;
    }

    public function getNoEtats(): ?Etats
    {
        return $this->noEtats;
    }

    public function setNoEtats(?Etats $noEtats): static
    {
        $this->noEtats = $noEtats;

        return $this;
    }
}
