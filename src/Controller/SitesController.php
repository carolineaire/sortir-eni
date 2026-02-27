<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Entity\Villes;
use App\Form\SitesType;
use App\Form\VillesType;
use App\Repository\SitesRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SitesController extends AbstractController
{
    #[Route('/sites', name: 'app_sites')]
    public function index(Request $request, SitesRepository $repository): Response
    {

        $search = $request->query->get('search');

        if ($search) {
            $sites = $repository->createQueryBuilder('v')
                ->where('v.nomSite LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $sites = $repository->findAll();
        }
        return $this->render('sites/gererSite.html.twig', [
            'sites' => $sites,
        ]);
    }



    #[Route('/sites/new', name: 'site_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $site = new Sites();
        $form = $this->createForm(SitesType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'Site ajouté avec succès !');
            return $this->redirectToRoute('app_sites');
        }

        return $this->render('sites/modifierSite.html.twig', [
            'siteForm' => $form,
            'titre' => 'Ajouter un site'
        ]);
    }

    #[Route('/sites/{id}/edit', name: 'site_edit')]
    public function edit(Sites $site, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SitesType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Site modifié avec succès !');
            return $this->redirectToRoute('app_sites');
        }

        return $this->render('sites/modifierSite.html.twig', [
            'siteForm' => $form,
            'titre' => 'Modifier le site'
        ]);
    }


    #[Route('/sites/{id}/delete', name: 'site_delete', methods: ['POST','GET'])]
    public function delete(Sites $site, EntityManagerInterface $em): Response
    {
        $em->remove($site);
        $em->flush();
        $this->addFlash('success', 'Site supprimée avec succès !');
        return $this->redirectToRoute('app_sites');
    }


}
