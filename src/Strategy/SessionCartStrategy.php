<?php
// src/Strategy/SessionCartStrategy.php

namespace App\Strategy;

use App\Entity\Product;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionCartStrategy implements CartStrategyInterface
{
    private $requestStack;
    private $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }

    private function getSessionCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    private function setSessionCart(array $cart): void
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }

    public function add(int $productId): void
    {
        $cart = $this->getSessionCart();
        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        $this->setSessionCart($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getSessionCart();
        unset($cart[$productId]);
        $this->setSessionCart($cart);
    }

    public function getFullCart(): array
    {
        $cart = $this->getSessionCart();
        $cartData = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'maxQuantity' => $product->getStock(),
                ];
            }
        }

        return $cartData;
    }

    public function increaseQuantity(int $productId): void
    {
        $this->add($productId);
    }

    public function decreaseQuantity(int $productId): void
    {
        $cart = $this->getSessionCart();

        if (isset($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId]--;
            } else {
                unset($cart[$productId]);
            }
            $this->setSessionCart($cart);
        }
    }

    public function getQuantity(Product $product): int
    {
        $cart = $this->getSessionCart();
        return $cart[$product->getId()] ?? 0;
    }

    public function removeCart(): void
    {
        $this->setSessionCart([]);
    }

    public function recoverCartFromOrder(Order $order): void
    {
        $sessionCart = $this->getSessionCart();

        foreach ($order->getProductLines() as $productLine) {
            $productId = $productLine->getProduct()->getId();
            $quantity = $productLine->getQuantity();

            if (isset($sessionCart[$productId])) {
                $sessionCart[$productId] += $quantity;
            } else {
                $sessionCart[$productId] = $quantity;
            }
        }

        $this->setSessionCart($sessionCart);
    }
}
