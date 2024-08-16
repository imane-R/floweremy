<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class CommandeRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Commande::class);
        $this->entityManager = $entityManager;
    }

    public function save(Commande $commande, bool $flush = false): void
    {
        $this->entityManager->persist($commande);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Commande $commande, bool $flush = false): void
    {
        $this->entityManager->remove($commande);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
