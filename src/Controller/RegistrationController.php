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

//            // 2) CRÉATION DU PARTICIPANT ASSOCIÉ
//            $participant = new Participants();
//            $participant->setUser($user); // Lien OneToOne
////var_dump($user);
//
//            // Champs Participants (mapped = false dans le form)
//
//            $participant->setNom($form->get('nom')->getData());
//            $participant->setPrenom($form->get('prenom')->getData());
//            $participant->setTelephone($form->get('telephone')->getData());
//            $participant->setMail($form->get('email')->getData());
//            $participant->setMotDePasse($user->getPassword());
//
//            $participant->setAdministrateur($form->get('administrateur')->getData());
//            $participant->setActif($form->get('actif')->getData());
//            $participant->setPseudo($user->getPseudo());
//
//
////            $siteId = $form->get('site')->getData();
////            $site = $entityManager->getRepository(Sites::class)->find($siteId);
////            $participant->setNoSites($site);
//            // $site = $entityManager->getRepository(Sites::class)->find($siteId);
//            // $participant->setNoSites($site);
//            $participant->setNoSites($form->get('site')->getData());
//
//            // 3) PERSISTENCE
//
//            $entityManager->persist($participant);
//            $entityManager->flush();

            // 4) LOGIN AUTOMATIQUE
            return $security->login($participant, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
