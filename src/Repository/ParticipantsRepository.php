<?php

namespace App\Repository;

use App\Entity\Participants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participants>
 */
class ParticipantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participants::class);
    }

    public function findUserById(int $id): ?Participants
    {
        return $this->find($id); 
    }

    public function findParticipantWithSite(int $id): ?Participants
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.site', 's')
            ->addSelect('s')
            ->andWhere('p.idParticipant = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
