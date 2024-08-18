<?php
// src/Service/CartService.php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $security;
    private $cartRepository;
    private $productRepository;
    private $requestStack;

    public function __construct(
        Security $security, // UserInterface might be null if the user is not logged in
        CartRepository $cartRepository,
        ProductRepository $productRepository,
        RequestStack $requestStack
    ) {
        $this->security = $security;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
    }

    private function getUser()
    {
        return $this->security->getUser();
    }

    private function getUserCart(): Cart
    {
        $user = $this->getUser();

        // Ensure the user is logged in
        if (!$user) {
            throw new \LogicException('User must be logged in to access the cart.');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->cartRepository->save($cart); // Repository handles saving
        }

        return $cart;
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
        if ($this->getUser()) {
            $this->addForLoggedInUser($productId);
        } else {
            $this->addForGuestUser($productId);
        }
    }

    private function addForLoggedInUser(int $productId): void
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

        $this->cartRepository->save($cart); // Repository handles saving
    }

    private function addForGuestUser(int $productId): void
    {
        $cart = $this->getSessionCart();

        if (isset($cart[$productId])) {
            $cart[$productId]++;
        } else {
            $cart[$productId] = 1;
        }

        $this->setSessionCart($cart);
    }

    public function remove(int $productId): void
    {
        if ($this->getUser()) {
            $this->removeForLoggedInUser($productId);
        } else {
            $this->removeForGuestUser($productId);
        }
    }

    private function removeForLoggedInUser(int $productId): void
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
            $this->cartRepository->remove($cartItem); // Repository handles removal
        }
    }

    private function removeForGuestUser(int $productId): void
    {
        $cart = $this->getSessionCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $this->setSessionCart($cart);
    }

    public function getFullCart(): array
    {
        if ($this->getUser()) {
            return $this->getFullCartForLoggedInUser();
        } else {
            return $this->getFullCartForGuestUser();
        }
    }

    private function getFullCartForLoggedInUser(): array
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

    private function getFullCartForGuestUser(): array
    {
        $cart = $this->getSessionCart();
        $cartData = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }

            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity,
                'maxQuantity' => $product->getStock(),
            ];
        }

        return $cartData;
    }

    public function transferCartToUser(): void
    {
        if (!$this->getUser()) {
            return;
        }

        $sessionCart = $this->getSessionCart();
        if (empty($sessionCart)) {
            return;
        }

        $cart = $this->getUserCart();

        foreach ($sessionCart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                continue;
            }

            $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
                return $item->getProduct() === $product;
            })->first() ?: new CartItem();

            $cartItem->setProduct($product);
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
            $cart->addItem($cartItem);
        }

        $this->cartRepository->save($cart); // Repository handles saving

        // Clear the session cart after transferring
        $this->setSessionCart([]);
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
        if ($this->getUser()) {
            $this->addForLoggedInUser($productId);
        } else {
            $this->addForGuestUser($productId);
        }
    }

    public function decreaseQuantity(int $productId): void
    {
        if ($this->getUser()) {
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
                    $this->cartRepository->remove($cartItem); // Repository handles removal
                }

                $this->cartRepository->save($cart); // Save the updated cart
            }
        } else {
            $cart = $this->getSessionCart();

            if (isset($cart[$productId])) {
                if ($cart[$productId] > 1) {
                    $cart[$productId]--;
                } else {
                    unset($cart[$productId]);
                }
            }

            $this->setSessionCart($cart);
        }
    }

    public function getQuantity(Product $product): int
    {
        if ($this->getUser()) {
            $cart = $this->getUserCart();
            $cartItem = $cart->getItems()->filter(function (CartItem $item) use ($product) {
                return $item->getProduct() === $product;
            })->first();

            return $cartItem ? $cartItem->getQuantity() : 0;
        } else {
            $cart = $this->getSessionCart();
            return $cart[$product->getId()] ?? 0;
        }
    }

    public function removeCart(): void
    {
        if ($this->getUser()) {
            $cart = $this->getUserCart();
            $this->cartRepository->remove($cart);
        } else {
            $this->setSessionCart([]);
        }
    }


    // src/Service/CartService.php

    public function recoverCartFromOrder(Order $order): void
    {
        $user = $this->getUser();

        if ($user) {
            // Recover cart for a logged-in user
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
        } else {
            // Recover session-based cart for a guest user
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
}
