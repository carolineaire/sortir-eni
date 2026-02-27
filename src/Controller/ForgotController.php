<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\ForgotFormType;

final class ForgotController extends AbstractController
{
    #[Route('/forgot', name: 'password_forgot')]
    public function forgot(Request $request, EntityManagerInterface $em): Response {
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);
        return $this->render('security/forgot.html.twig', [
        ]);
    }
}
