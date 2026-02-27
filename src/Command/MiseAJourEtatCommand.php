<?php
namespace App\Command;

use App\Repository\SortiesRepository;
use App\Repository\EtatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class MiseAJourEtatCommand extends Command
{
    protected static $defaultName = 'app:update-sortie-states';

    private $sortieRepository;
    private $etatRepository;
    private $em;

    public function __construct(SortiesRepository $sortieRepository, EtatsRepository $etatRepository, EntityManagerInterface $em)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->em = $em;
        parent::__construct(); // <-- à la fin
    }

    protected function configure()
    {
        $this->setDescription('Met à jour automatiquement les états des sorties selon la date de clôture, date de début et durée.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new DateTime();
        $sorties = $this->sortieRepository->findAll();

        foreach ($sorties as $sortie) {
            $etatId = null;

            // Calcul de la date de fin de la sortie
            $dateFin = (clone $sortie->getDateDebut())->modify('+' . $sortie->getDuree() . ' hours');

            // 1) Clôturé si la date de clôture est dépassée
            if ($now > $sortie->getDateCloture() && $now < $sortie->getDateDebut()) {
                $etatId = 3; // Clôturé
            }
            // 2) Activité en cours si maintenant est entre dateDebut et dateFin
            if ($now >= $sortie->getDateDebut() && $now <= $dateFin) {
                $etatId = 4; // En cours
            }
            // 3) Sortie passée si dateFin dépassée
            if ($now > $dateFin) {
                $etatId = 6; // Terminé
            }

            // Mise à jour si l'état a changé
            if ($etatId && $sortie->getEtat()->getId() !== $etatId) {
                $nouvelEtat = $this->etatRepository->find($etatId);
                if ($nouvelEtat) {
                    $sortie->setEtat($nouvelEtat);
                    $output->writeln("Sortie ID {$sortie->getId()} mise à jour à l'état '{$nouvelEtat->getLibelle()}'");
                }
            }
        }

        $this->em->flush();
        $output->writeln('✅ Tous les états des sorties ont été mis à jour.');

        return Command::SUCCESS;
    }
}