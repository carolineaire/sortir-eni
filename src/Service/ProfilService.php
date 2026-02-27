<?php

namespace App\Service;
use App\Repository\ParticipantsRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfilService
{
    public function __construct(private ParticipantsRepository $repository) {
        
    }

    public function getUserProfil(int $id)
    {
        $user = $this->repository->findParticipantWithSite($id);

        if (!$user) {
            throw new NotFoundHttpException("Utilisateur introuvable !");
        }

        return $user;
    }

    /**
     * Récupère un utilisateur par email et téléphone
     * Retourne null si pas trouvé (au lieu de lancer une exception)
     */
    public function getParticipantsWithMailAndNumberPhone(string $email, string $phoneNumber): ?object
    {
        return $this->repository->findParticipantWithMailAndPhoneNumber($email, $phoneNumber);
    }
}