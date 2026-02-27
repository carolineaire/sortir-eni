<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ForgotFormType;
use App\Service\ProfilService;

final class ForgotController extends AbstractController
{
    #[Route('/forgot', name: 'password_forgot')]
    public function forgot(
        Request $request, 
        EntityManagerInterface $em, 
        ProfilService $profilService,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $form = $this->createForm(ForgotFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();
            $email = $data['email'] ?? null;
            $telephone = $data['telephone'] ?? null;
            
            if ($email && $telephone) {
                // Récupérer l'utilisateur
                $user = $profilService->getParticipantsWithMailAndNumberPhone($email, $telephone);

                if ($user) {
                    // Réinitialiser le mot de passe à "1234567"
                    $hashedPassword = $userPasswordHasher->hashPassword($user, "1234567");
                    $user->setPassword($hashedPassword);
                    
                    // Sauvegarder en base de données
                    $em->persist($user);
                    $em->flush();
                    
                    // Message de succès
                    $this->addFlash('success', 'Votre mot de passe a été réinitialisé à 1234567');
                } else {
                    // Message d'erreur
                    $this->addFlash('error', 'Aucun utilisateur trouvé avec ces informations');
                }
            } else {
                $this->addFlash('error', 'Email et téléphone sont requis');
            }
            
            // Redirection
            return $this->redirectToRoute('password_forgot');
        }

        return $this->render('security/forgot.html.twig', [
            'forgotForm' => $form,
        ]);
    }
}