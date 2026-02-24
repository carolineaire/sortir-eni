<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SortiesRepository;

final class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie')]
    public function index(SortiesRepository $repo): Response
    {
        $sorties = $repo->findAll();
        return $this->render('sortie/sortie.html.twig', [
            'sortie' => $sorties,
        ]);
    }
}
