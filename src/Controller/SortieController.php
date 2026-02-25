<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Entity\Villes;
use App\Form\SortiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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


    #[Route('/creerSortie', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $site = $user->getNoSites(); // instance de Sites
        $ville = $this->deduireVilleDepuisSite($site->getNomSite());
        $sortie = new Sorties();


        $sortieForm = $this->createForm(SortiesType::class, $sortie);
        $sortieForm->get('villeOrganisatrice')->setData($ville);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted()) {

            if ($sortieForm->get('annuler')->isClicked()) {

                return $this->redirectToRoute('app_sortie');
            }
            if ($sortieForm->isValid()) {
                $dateDebut = $sortie->getDateDebut();
                $dateCloture = $sortie->getDateCloture();

                if ($dateDebut >= $dateCloture) {
                    $this->addFlash('error', 'La date de clôture doit être supérieure à la date de debut.');
                    return $this->redirectToRoute('sortie_create');

                }
                // Organisateur = utilisateur connecté
                $organisateur = $this->getUser();
                $IdOrganisateur = $organisateur->getId();
                $sortie->setOrganisateur($IdOrganisateur);
                // État par défaut = "Créée"
                //  $etatCree = $em->getRepository(Etats::class)->find(1);
                // $sortie->setNoEtats($etatCree);

                if ($sortieForm->get('enregistrer')->isClicked()) {

                    $etat = $em->getRepository(Etats::class)->find(1);
                }
                if ($sortieForm->get('publier')->isClicked()) {
                    $etat = $em->getRepository(Etats::class)->find(2); // Ouverte
                }
                $sortie->setNoEtats($etat);

                $em->persist($sortie);
                $em->flush();
                return $this->redirectToRoute('app_sortie');
            }
        }
            return $this->render('sortie/creerSortie.html.twig', [
                'sortieForm' => $sortieForm
            ]);
        }

    private function deduireVilleDepuisSite(string $nomSite): string
    {
        if (str_contains($nomSite, 'Nantes')) {
            return 'Nantes';
        }
        if (str_contains($nomSite, 'Rennes')) {
            return 'Rennes';
        }
        if (str_contains($nomSite, 'Quimper')) {
            return 'Quimper';
        }
        if (str_contains($nomSite, 'Niort')) {
            return 'Niort';
        }

        return 'Ville inconnue';
    }
    #[Route('/ajax/lieux/{id}', name: 'ajax_lieux')]
    public function ajaxLieux(Villes $ville): JsonResponse
    {
        $lieux = $ville->getLieuxes();

        $data = [];
        foreach ($lieux as $lieu) {
            $data[] = [
                'id' => $lieu->getId(),
                'nom' => $lieu->getNomLieu(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/ajax/lieu/{id}', name: 'ajax_lieu')]
    public function ajaxLieu(Lieux $lieu): JsonResponse
    {
        return new JsonResponse([
            'rue' => $lieu->getRue(),
            'cp' => $lieu->getNoVilles()->getCpo(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
        ]);
    }

}
