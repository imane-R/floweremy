<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CartRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Cart::class);
        $this->entityManager = $entityManager;
    }

    public function save(Cart $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function remove(Cart $cart): void
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }
}
