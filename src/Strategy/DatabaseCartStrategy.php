<?php
// src/Strategy/DatabaseCartStrategy.php

namespace App\Strategy;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;

class DatabaseCartStrategy implements CartStrategyInterface
{
    private $cartRepository;
    private $productRepository;
    private $security;

    public function __construct(CartRepository $cartRepository, ProductRepository $productRepository, Security $security)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;
    }

    private function getUserCart(): Cart
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('User must be logged in to access the cart.');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->cartRepository->save($cart);
        }

        return $cart;
    }

    public function add(int $productId): void
    {
        $cart = $this->getUserCart();
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
            return $item->getProduct() === $product;
        })->first() ?: new CartItem();

        if ($cartItem->getId()) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem->setProduct($product);
            $cartItem->setQuantity(1);
            $cart->addItem($cartItem);
        }

        $this->cartRepository->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getUserCart();
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
            return $item->getProduct() === $product;
        })->first();

        if ($cartItem) {
            $cart->removeItem($cartItem);
            $this->cartRepository->remove($cartItem);
        }
    }

    public function getFullCart(): array
    {
        $cart = $this->getUserCart();
        $cartData = [];

        foreach ($cart->getItems() as $item) {
            $cartData[] = [
                'product' => $item->getProduct(),
                'quantity' => $item->getQuantity(),
                'maxQuantity' => $item->getProduct()->getStock(),
            ];
        }

        return $cartData;
    }

    public function increaseQuantity(int $productId): void
    {
        $this->add($productId);
    }

    public function decreaseQuantity(int $productId): void
    {
        $cart = $this->getUserCart();
        $product = $this->productRepository->find($productId);

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
            return $item->getProduct() === $product;
        })->first();

        if ($cartItem) {
            if ($cartItem->getQuantity() > 1) {
                $cartItem->setQuantity($cartItem->getQuantity() - 1);
            } else {
                $cart->removeItem($cartItem);
                $this->cartRepository->remove($cartItem);
            }

            $this->cartRepository->save($cart);
        }
    }

    public function getQuantity(Product $product): int
    {
        $cart = $this->getUserCart();
        $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
            return $item->getProduct() === $product;
        })->first();

        return $cartItem ? $cartItem->getQuantity() : 0;
    }

    public function removeCart(): void
    {
        $cart = $this->getUserCart();
        $this->cartRepository->remove($cart);
    }

    public function recoverCartFromOrder(Order $order): void
    {
        $cart = $this->getUserCart();

        foreach ($order->getProductLines() as $productLine) {
            $product = $productLine->getProduct();
            $quantity = $productLine->getQuantity();

            $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
                return $item->getProduct() === $product;
            })->first() ?: new CartItem();

            if ($cartItem->getId()) {
                $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
            } else {
                $cartItem->setProduct($product);
                $cartItem->setQuantity($quantity);
                $cart->addItem($cartItem);
            }
        }

        $this->cartRepository->save($cart);
    }

    public function addProductWithQuantity(Product $product, int $quantity): void
    {
        $cart = $this->getUserCart();

        $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
            return $item->getProduct() === $product;
        })->first() ?: new CartItem();

        if ($cartItem->getId()) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $cart->addItem($cartItem);
        }

        $this->cartRepository->save($cart);
    }
}
