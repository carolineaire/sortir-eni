<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Inscriptions;
use App\Entity\Sorties;
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

    #[Route('/sortie/{id}/inscription', name: 'sortie_inscription')]
    public function inscription(int $id, SortiesRepository $sortiesRepository, EntityManagerInterface $em): RedirectResponse {
        $sortie = $sortiesRepository->find($id);
    
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie introuvable.');
        }
    
        $user = $this->getUser();
    
        if (!$user instanceof \App\Entity\Participants) {
            throw new \LogicException('L\'utilisateur doit être un participant.');
        }
    
        // Vérifier si déjà inscrit
        foreach ($sortie->getInscriptions() as $inscription) {
            if ($inscription->getNoParticipants() === $user) {
                return $this->redirectToRoute('app_sortie');
            }
        }
    
        $inscription = new Inscriptions();
        $inscription->setNoParticipants($user);
        $inscription->setNoSorties($sortie);
        $inscription->setDateInscription(new \DateTimeImmutable());
    
        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', 'Inscrit avec succès!');
    
        return $this->redirectToRoute('app_sortie');
    }

    #[Route('/sortie/desinscription/{id}', name: 'sortie_desinscription')]
    public function desinscription(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour vous désinscrire.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la sortie
        $sortie = $em->getRepository(Sorties::class)->find($id);
        if (!$sortie) {
            $this->addFlash('danger', 'Sortie introuvable.');
            return $this->redirectToRoute('app_sortie');
        }

        // Chercher l'inscription de l'utilisateur à cette sortie
        $inscription = $em->getRepository(Inscriptions::class)
            ->findOneBy([
                'noParticipants' => $user,
                'noSorties' => $sortie
            ]);

        if (!$inscription) {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
            return $this->redirectToRoute('app_sortie');
        }

        $em->remove($inscription);
        $em->flush();

        $this->addFlash('success', 'Vous avez bien été désinscrit.');

        return $this->redirectToRoute('app_sortie');
    }
}
