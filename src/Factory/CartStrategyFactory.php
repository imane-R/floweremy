<?php
// src/Factory/CartStrategyFactory.php

namespace App\Factory;

use App\Strategy\CartStrategyInterface;
use App\Strategy\DatabaseCartStrategy;
use App\Strategy\SessionCartStrategy;
use Symfony\Bundle\SecurityBundle\Security;

class CartStrategyFactory
{
    private $databaseCartStrategy;
    private $sessionCartStrategy;
    private $security;

    public function __construct(DatabaseCartStrategy $databaseCartStrategy, SessionCartStrategy $sessionCartStrategy, Security $security)
    {
        $this->databaseCartStrategy = $databaseCartStrategy;
        $this->sessionCartStrategy = $sessionCartStrategy;
        $this->security = $security;
    }

    public function getStrategy(): CartStrategyInterface
    {
        if ($this->security->getUser()) {
            $this->transferCartToUser();
            return $this->databaseCartStrategy;
        }

        return $this->sessionCartStrategy;
    }

    private function transferCartToUser(): void
    {
        $sessionCart = $this->sessionCartStrategy->getFullCart();
        if (empty($sessionCart)) {
            return;
        }

        foreach ($sessionCart as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            $this->databaseCartStrategy->addProductWithQuantity($product, $quantity);
        }

        // Clear the session cart after transferring
        $this->sessionCartStrategy->removeCart();
    }
}
