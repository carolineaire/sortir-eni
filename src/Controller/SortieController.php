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
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Inscriptions;
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
                $sortie->setOrganisateur($organisateur);
                // État par défaut = "Créée"
                //  $etatCree = $em->getRepository(Etats::class)->find(1);
                // $sortie->setNoEtats($etatCree);

                if ($sortieForm->get('enregistrer')->isClicked()) {

                    $etat = $em->getRepository(Etats::class)->find(1); //Créer
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

    #[Route('/sortie/{id}/annuler', name: 'sortie_annuler')]
    public function annuler(int $id, EntityManagerInterface $em): RedirectResponse
    {
        $sortie = $em->getRepository(Sorties::class)->find($id);

        if (!$sortie) {
            $this->addFlash('danger', 'Sortie introuvable.');
            return $this->redirectToRoute('app_sortie');
        }

        $user = $this->getUser();

        // Vérification sécurité : organisateur ou admin uniquement
        if ($sortie->getOrganisateur() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler cette sortie.');
        }

        // Etat "Annulée" (remplace 6 par l'id correct)
        $etatAnnule = $em->getRepository(Etats::class)->find(5);

        if (!$etatAnnule) {
            $this->addFlash('danger', 'Etat "Annulée" introuvable.');
            return $this->redirectToRoute('app_sortie');
        }

        $sortie->setNoEtats($etatAnnule);

        $em->flush();

        $this->addFlash('success', 'La sortie a bien été annulée.');

        return $this->redirectToRoute('app_sortie');
    }
}
