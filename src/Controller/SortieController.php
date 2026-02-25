<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SortieService;
use App\Repository\SortiesRepository;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(SortiesRepository $repo): Response
    {
        $sorties = $repo->findAll();
        $user = $this->getUser();

        $villes = [];
        foreach ($sorties as $s) {
            $nomVille = $s->getNoLieux()->getNoVilles()->getNomVille();
            if (!in_array($nomVille, $villes)) {
                $villes[] = $nomVille;
            }
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('sortie/sortie.html.twig', [
            'sortie' => $sorties,
            'ville' => $villes,
            'user' => $user
        ]);
    }

    #[Route('/sortie/{id}', name: 'sortie')]
    public function profil(int $id, SortieService $sortieService): Response
    {
        $sortie = $sortieService->getSortieDetails($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie introuvable.');
        }

        return $this->render('sortie/sortie-details.html.twig', [
            'sortie' => $sortie
        ]);
    }
}
