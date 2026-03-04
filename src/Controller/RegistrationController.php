<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Entity\User;
use App\Entity\Participants;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response
    {
        $participant = new Participants();
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1) HASH DU MOT DE PASSE
            $plainPassword = $form->get('plainPassword')->getData();

            $participant->setPassword(
                $userPasswordHasher->hashPassword($participant, $plainPassword)
            );
        if($participant->isAdministrateur()) {

            $participant->setRoles(['ROLE_ADMIN']);
        }else{
            $participant->setRoles(['ROLE_USER']);
        }
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        //si pas d'user connecté, redirection vers la page de connexion
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
