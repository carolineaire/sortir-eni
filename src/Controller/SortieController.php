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
        $ville = $site->getNomSite();
        $sortie = new Sorties();


        $sortieForm = $this->createForm(SortiesType::class, $sortie);
        $sortieForm->get('villeOrganisatrice')->setData($ville);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted()) {

            if ($sortieForm->isValid()) {
                $dateDebut = $sortie->getDateDebut();
                $dateCloture = $sortie->getDateCloture();

                if ($dateDebut <= $dateCloture) {
                    $this->addFlash('error', 'La date et heure de la sortie doit être supérieure à la date limite d\'inscription !');
                    return $this->redirectToRoute('sortie_create');

                }
                // Organisateur = utilisateur connecté


                $sortie->setOrganisateur($this->getUser());
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




    #[Route('/sortie/{id}/modifier', name: 'sortie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Sorties $sortie,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {

        $organisateur = $sortie->getOrganisateur();
        $siteOrganisateur = $organisateur->getNoSites() ;
        $nomSite = $siteOrganisateur->getNomSite() ;

        $villeOrganisatrice =  $nomSite ;


        $form = $this->createForm(SortiesType::class, $sortie);


        if ($villeOrganisatrice) {
            $form->get('villeOrganisatrice')->setData($villeOrganisatrice);
        }


        $lieu = $sortie->getNoLieux();
        if ($lieu) {
            $form->get('noLieux')->setData($lieu);

            $villeDuLieu = $lieu->getNoVilles();
            if ($villeDuLieu) {
                $form->get('noVilles')->setData($villeDuLieu);
                $rue = $lieu->getRue();
                $cp = $villeDuLieu->getCpo();
                $latitude = $lieu->getLatitude();
                $longitude = $lieu->getLongitude();
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification des dates
            if ($sortie->getDateDebut() <= $sortie->getDateCloture()) {
                $this->addFlash('error', 'La date de début doit être supérieure à la date limite d\'inscription.');
                return $this->redirectToRoute('sortie_edit', ['id' => $sortie->getId()]);
            }

            // Gestion des boutons
            if ($form->get('enregistrer')->isClicked()) {
                $etat = $em->getRepository(Etats::class)->find(1); // Créée
            }

            if ($form->get('publier')->isClicked()) {
                $etat = $em->getRepository(Etats::class)->find(2); // Ouverte
            }

            if (isset($etat)) {
                $sortie->setNoEtats($etat);
            }

            // Pas de persist() l'entité existe déjà
            $em->flush();

            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('sortie/modifierSortie.html.twig', [
            'sortieForm' => $form,
            'sortie' => $sortie,
            'rue' => $lieu ? $lieu->getRue() : '',
            'codePostal' => $villeDuLieu ? $villeDuLieu->getCpo() : '',
            'latitude' => $lieu ? $lieu->getLatitude() : '',
            'longitude' => $lieu ? $lieu->getLongitude() : '',
            'ville' => $villeDuLieu ? $villeDuLieu->getNomVille() : '',
        ]);
    }



    #[Route('/sortie/{id}/delete', name: 'sortie_delete', methods: ['GET'])]
    public function delete(Sorties $sortie, EntityManagerInterface $em): Response
    {
        //  check si l'utilisateur est l'organisateur
        // if ($this->getUser() !== $sortie->getOrganisateur()) {
        //     $this->addFlash('error', 'Vous ne pouvez pas supprimer cette sortie.');
        //     return $this->redirectToRoute('app_sortie');
        // }

        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', 'La sortie a bien été supprimée.');

        return $this->redirectToRoute('app_sortie');
    }






}
