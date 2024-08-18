<?php
// src/Strategy/CartStrategyInterface.php

namespace App\Strategy;

use App\Entity\Product;
use App\Entity\Order;

interface CartStrategyInterface
{
    public function add(int $productId): void;
    public function remove(int $productId): void;
    public function getFullCart(): array;
    public function increaseQuantity(int $productId): void;
    public function decreaseQuantity(int $productId): void;
    public function getQuantity(Product $product): int;
    public function removeCart(): void;
    public function recoverCartFromOrder(Order $order): void;
}
