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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;


final class ProfilController extends AbstractController
{
    #[Route('/monProfil', name: 'app_profil')]
    public function index(ProfilService $profilService): Response
    {
        $idCurrent = $this->getUser()->getId();
        $user = $profilService->getUserProfil($idCurrent);

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
            'myprofil' => true
        ]);
    }

    #[Route('/profil/{id}', name: 'app_profil_user')]
    public function profil(int $id, ProfilService $profilService): Response
    {
        $idCurrent = $this->getUser()->getId();
        if ($idCurrent == $id){
            $index = $this->index($profilService);
            return $index;
        } else {
            $user = $profilService->getUserProfil($id);

            return $this->render('profil/profil.html.twig', [
                'user' => $user,
                'myprofil' => false
            ]);
        }
    }

    #[Route('/EditProfil', name: 'edit_participant')]
    public function editParticipant(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        ProfilService $profilService,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/images/user')] string $pictureUserDirectory
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

            // 5) Gérer l'image
            $imageFile = $form->get('userPicture')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move($pictureUserDirectory, $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $participant->setImage($newFilename);
            }
            
            // 6) METTRE À JOUR les rôles
            if ($participant->isAdministrateur()) {
                $participant->setRoles(['ROLE_ADMIN']);
            } else {
                $participant->setRoles(['ROLE_USER']);
            }
            
            // 7) SAUVEGARDER
            $entityManager->flush();
            
            // 8) REDIRECTION
            return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
        }
        
        // 9) AFFICHER le formulaire
        return $this->render('profil/edit.html.twig', [
            'editProfilForm' => $form,
            'participant' => $participant,
            'user' => $user,
        ]);
    }
}