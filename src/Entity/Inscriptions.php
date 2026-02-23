<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{



    #[ORM\Column]
    private ?\DateTimeImmutable $dateInscription = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn( name: "participants_no_participant", referencedColumnName: "no_participant", nullable: false )]
    private ?Participants $noParticipants = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn( name: "sorties_no_sortie", referencedColumnName: "no_sortie", nullable: false )]
    private ?Sorties $noSorties = null;



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
