<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LieuxController extends AbstractController
{
    #[Route('/lieu/new', name: 'lieu_new')]
    public function new(Request $request, EntityManagerInterface $em): Response {
        $lieu = new Lieux();
        $form = $this->createForm(LieuxType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu); $em->flush();
            return $this->redirectToRoute('sortie_create');
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('lieux/new.html.twig', [
            'form' => $form->createView(),
            ]);
    }
}
