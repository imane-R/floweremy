<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Order::class);
        $this->entityManager = $entityManager;
    }

    public function save(Order $Order, bool $flush = false): void
    {
        $this->entityManager->persist($Order);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function remove(Order $Order, bool $flush = false): void
    {
        $this->entityManager->remove($Order);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}
