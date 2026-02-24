<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ProfilService;

final class ProfilController extends AbstractController
{
    #[Route('/monProfil', name: 'app_profil')]
    public function index(ProfilService $profilService): Response
    {

        $user = $profilService->getUserProfil(1);

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'myprofil' => true
        ]);
    }

    #[Route('/profil/{id}', name: 'profil')]
    public function profil(int $id, ProfilService $profilService): Response
    {
        $user = $profilService->getUserProfil($id);

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'myprofil' => false
        ]);
    }
}
