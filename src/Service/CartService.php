<?php
// src/Service/CartService.php
namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Factory\CartStrategyFactory;

class CartService
{
    private $cartStrategyFactory;

    public function __construct(CartStrategyFactory $cartStrategyFactory)
    {
        $this->cartStrategyFactory = $cartStrategyFactory;
    }

    public function add(int $productId): void
    {
        $this->cartStrategyFactory->getStrategy()->add($productId);
    }

    public function remove(int $productId): void
    {
        $this->cartStrategyFactory->getStrategy()->remove($productId);
    }

    public function getFullCart(): array
    {
        return $this->cartStrategyFactory->getStrategy()->getFullCart();
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function increaseQuantity(int $productId): void
    {
        $this->cartStrategyFactory->getStrategy()->increaseQuantity($productId);
    }

    public function decreaseQuantity(int $productId): void
    {
        $this->cartStrategyFactory->getStrategy()->decreaseQuantity($productId);
    }

    public function getQuantity(Product $product): int
    {
        return $this->cartStrategyFactory->getStrategy()->getQuantity($product);
    }

    public function removeCart(): void
    {
        $this->cartStrategyFactory->getStrategy()->removeCart();
    }

    public function recoverCartFromOrder(Order $order): void
    {
        $this->cartStrategyFactory->getStrategy()->recoverCartFromOrder($order);
    }
}
