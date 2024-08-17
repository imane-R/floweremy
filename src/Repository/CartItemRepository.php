<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CartItemRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, CartItem::class);
        $this->entityManager = $entityManager;
    }

    /**
     * Save a CartItem entity.
     *
     * @param CartItem $cartItem
     */
    public function save(CartItem $cartItem): void
    {
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    /**
     * Remove a CartItem entity.
     *
     * @param CartItem $cartItem
     */
    public function remove(CartItem $cartItem): void
    {
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }

    /**
     * Find a CartItem by Cart and Product.
     *
     * @param int $cartId
     * @param int $productId
     *
     * @return CartItem|null
     */
    public function findOneByCartAndProduct(int $cartId, int $productId): ?CartItem
    {
        return $this->createQueryBuilder('ci')
            ->andWhere('ci.cart = :cart')
            ->andWhere('ci.product = :product')
            ->setParameter('cart', $cartId)
            ->setParameter('product', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
