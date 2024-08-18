<?php

namespace App\Repository;

use App\Entity\Mletpc;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Mletpc>
 */
class MletpcRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Mletpc::class);
        $this->entityManager = $entityManager;
    }

    public function save(Mletpc $mletpc): void
    {
        $this->entityManager->persist($mletpc);
        $this->entityManager->flush();
    }

    public function remove(Mletpc $mletpc): void
    {
        $this->entityManager->remove($mletpc);
        $this->entityManager->flush();
    }


}
