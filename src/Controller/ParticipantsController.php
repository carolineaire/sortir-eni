<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Entity\Sites;
use App\Repository\EtatsRepository;
use App\Repository\ParticipantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\File;
final class ParticipantsController extends AbstractController
{
    #[Route('/participants', name: 'app_participants')]
    public function index(ParticipantsRepository $repo,Request $request): Response
    {
        // Vérifie que l'utilisateur est admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $search = $request->query->get('search', ''); // récupère ?search=xxx

        $qb = $repo->createQueryBuilder('p')
            ->where('p.pseudo NOT LIKE :deleted')
            ->setParameter('deleted', 'supprime%');

        if ($search) {
            $qb->andWhere('p.pseudo LIKE :search OR p.nom LIKE :search OR p.prenom LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        $users = $qb->orderBy('p.pseudo', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('participants/users-list.html.twig', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    #[Route('/participants/{id}/toggle', name: 'participants_toggleUser')]
    public function toggleUser(Participants $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Empêche un admin de se désactiver lui-même
        if ($this->getUser() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre compte.');
            return $this->redirectToRoute('app_participants');
        }

        // Bascule le statut actif/inactif
        $user->setActif(!$user->isActif());
        $em->flush();

        $this->addFlash('success', 'Statut du compte modifié avec succès.');

        return $this->redirectToRoute('app_participants');
    }


    #[Route('/participants/{id}/delete', name: 'participants_deleteUser')]
    public function deleteUser(
        Participants $user,
        EntityManagerInterface $em,
        EtatsRepository $etatsRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $admin = $this->getUser();

        // Empêche un admin de se supprimer lui-même
        if ($admin === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_participants');
        }

        // Récupérer l'état "Annulée" (id = 5)
        $etatAnnule = $etatsRepository->find(5);

        // Transférer les sorties
        foreach ($user->getSorties() as $sortie) {

            $sortie->setNoEtats($etatAnnule);
            $sortie->setMotifAnnulation("Sortie annulée suite à la suppression du compte organisateur.");
        }

        // Trouver le prochain numéro pour "Utilisateur supprimé X"
        $ghostCount = $em->getRepository(Participants::class)
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.nom LIKE :nom')
            ->setParameter('nom', 'Utilisateur supprimé%')
            ->getQuery()
            ->getSingleScalarResult();

        $num = $ghostCount + 1;
        $hashedPassword = $passwordHasher->hashPassword($user, '1234567');
        // Anonymiser le participant
        $user->setNom('Utilisateur supprimé '.$num);
        $user->setPrenom('Supprimé');
        $user->setEmail('supprime'.$num.'@example.com');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setTelephone("0000000000");
        $user->setPseudo("supprime".$num);
        $user->setImage(" ");
        $user->setAdministrateur(false);
        // Optionnel : désactiver le compte
        $user->setActif(false);
        $em->flush();

        $this->addFlash(
            'success',
            'Utilisateur anonymisé avec succès.Ses sorties ont été  mises en état annulé.'
        );

        return $this->redirectToRoute('app_participants');
    }



    #[Route('/participants/importcsv', name: 'participants_importCsv')]
    public function importCsv(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createFormBuilder()
            ->add('csv_file', FileType::class, [
                'label' => 'Fichier CSV',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'text/plain',
                            'text/csv',
                            'application/csv',
                            'text/comma-separated-values',
                            'application/vnd.ms-excel',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier CSV valide',
                    ])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csv_file')->getData();
            $path = $csvFile->getRealPath();
            $rows = array_map('str_getcsv', file($path));
            $header = array_map('trim', array_shift($rows));

            foreach ($rows as $i => $row) {
                $data = array_combine($header, $row);

                try {
                    $user = new Participants();
                    $user->setPseudo($data['pseudo']);
                    $user->setNom($data['nom']);
                    $user->setPrenom($data['prenom']);
                    $user->setEmail($data['email']);
                    $user->setTelephone($data['telephone'] ?? null);
                    $user->setActif(strtolower($data['actif'] ?? 'true') === 'true');
                    $user->setAdministrateur(strtolower($data['administrateur'] ?? 'false') === 'true');

                    if ($user->isAdministrateur()) {

                        $user->setRoles(['ROLE_ADMIN']);
                    } else {

                        $user->setRoles(['ROLE_USER']);
                    }
                    // Hash du mot de passe
                    $plainPassword = $data['plainPassword'] ?? 'changeme';
                    $user->setPassword($hasher->hashPassword($user, $plainPassword));

                    // Associer le site si indiqué
                    if (!empty($data['noSites'])) {
                        $site = $em->getRepository(Sites::class)->find($data['noSites']);
                        if ($site) {
                            $user->setNoSites($site);
                        }
                    }

                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', "Utilisateur {$user->getPseudo()} importé avec succès.");
                } catch (\Exception $e) {
                    $this->addFlash('error', "Erreur ligne ".($i+2).": ".$e->getMessage());
                }
            }

            return $this->redirectToRoute('app_participants');
        }

        return $this->render('participants/users-import-csv.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
