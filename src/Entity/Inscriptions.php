<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participants $noParticipants = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sorties $noSorties = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeImmutable
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeImmutable $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getNoParticipants(): ?Participants
    {
        return $this->noParticipants;
    }

    public function setNoParticipants(?Participants $noParticipants): static
    {
        $this->noParticipants = $noParticipants;

        return $this;
    }

    public function getNoSorties(): ?Sorties
    {
        return $this->noSorties;
    }

    public function setNoSorties(?Sorties $noSorties): static
    {
        $this->noSorties = $noSorties;

        return $this;
    }
}
