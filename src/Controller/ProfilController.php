<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ProfilService;
use App\Entity\Participants;
use App\Form\EditProfilFormType;


final class ProfilController extends AbstractController
{
    #[Route('/monProfil', name: 'app_profil')]
    public function index(ProfilService $profilService): Response
    {
        $idCurrent = $this->getUser()->getId();
        $user = $profilService->getUserProfil($idCurrent);

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

    #[Route('/EditProfil', name: 'edit_participant')]
    public function editParticipant(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        ProfilService $profilService
    ): Response
    {
        // 1) RÉCUPÉRER le participant en base
        $idCurrent = $this->getUser()->getId();
        $user = $profilService->getUserProfil($idCurrent);
        $participant = $entityManager->getRepository(Participants::class)->find($idCurrent);
        
        // Vérifier qu'il existe
        if (!$participant) {
            throw $this->createNotFoundException('Participant non trouvé');
        }
        
        // 2) CRÉER le formulaire avec le participant existant
        $form = $this->createForm(EditProfilFormType::class, $participant);
        $form->handleRequest($request);
        
        // 3) SI le formulaire est envoyé ET valide
        if ($form->isSubmitted() && $form->isValid()) {
            
            // 4) GÉRER le mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {  // Si le champ n'est pas vide
                $participant->setPassword(
                    $userPasswordHasher->hashPassword($participant, $plainPassword)
                );
            }
            
            // 5) METTRE À JOUR les rôles
            if ($participant->isAdministrateur()) {
                $participant->setRoles(['ROLE_ADMIN']);
            } else {
                $participant->setRoles(['ROLE_USER']);
            }
            
            // 6) SAUVEGARDER
            $entityManager->flush();
            
            // 7) REDIRECTION
            return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
        }
        
        // 8) AFFICHER le formulaire
        return $this->render('profil/edit.html.twig', [
            'editProfilForm' => $form,
            'participant' => $participant,
            'user' => $user,
        ]);
    }
}