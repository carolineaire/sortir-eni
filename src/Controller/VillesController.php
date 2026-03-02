<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Form\VillesType;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VillesController extends AbstractController
{
    #[Route('/villes', name: 'app_villes')]
    public function index(Request $request, VillesRepository $repository): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $villes = $repository->createQueryBuilder('v')
                ->where('v.nom_ville LIKE :search')
                ->orWhere('v.cpo LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $villes = $repository->findAll();
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('villes/gererVille.html.twig', [
            'villes' => $villes,
        ]);
    }
    #[Route('/villes/new', name: 'ville_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Villes();
        $form = $this->createForm(VillesType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ville);
            $em->flush();
            $this->addFlash('success', 'Ville ajoutée avec succès !');
            return $this->redirectToRoute('app_villes');
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('villes/formVille.html.twig', [
            'villeForm' => $form,
            'titre' => 'Ajouter une ville'
        ]);
    }

    #[Route('/villes/{id}/edit', name: 'ville_edit')]
    public function edit(Villes $ville, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VillesType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Ville modifiée avec succès !');
            return $this->redirectToRoute('app_villes');
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('villes/modifierVille.html.twig', [
            'villeForm' => $form,
            'titre' => 'Modifier la ville'
        ]);
    }

    //  Supprimer une ville
    #[Route('/villes/{id}/delete', name: 'ville_delete', methods: ['POST','GET'])]
    public function delete(Villes $ville, EntityManagerInterface $em): Response
    {
        $em->remove($ville);
        $em->flush();
        $this->addFlash('success', 'Ville supprimée avec succès !');
        return $this->redirectToRoute('app_villes');
    }




}
