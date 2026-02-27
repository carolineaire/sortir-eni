<?php

namespace App\Repository;

use App\Entity\Participants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Participants>
 */
class ParticipantsRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participants::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Participants) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserById(int $id): ?Participants
    {
        return $this->find($id); 
    }

    public function findParticipantWithSite(int $id): ?Participants
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.noSites', 's')
            ->addSelect('s')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findParticipantWithMailAndPhoneNumber(string $email, string $phoneNumber): ?Participants
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.noSites', 's')
            ->addSelect('s')
            ->andWhere('p.email = :email')
            ->andWhere('p.telephone = :phoneNumber')
            ->setParameter('email', $email)
            ->setParameter('phoneNumber', $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
