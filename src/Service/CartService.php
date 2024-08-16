<?php
// src/Service/CartService.php

namespace App\Service;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $produitRepository;

    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository)
    {
        $this->requestStack = $requestStack;
        $this->produitRepository = $produitRepository;
    }

    public function add(int $id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
    }

    public function remove(int $id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
    }

    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $cartData = [];

        foreach ($cart as $id => $quantity) {
            $produit = $this->produitRepository->find($id);
            if (!$produit) {
                continue;
            }

            $cartData[] = [
                'produit' => $produit,
                'quantity' => $quantity,
                'maxQuantity' => $produit->getStock(),
            ];
        }

        return $cartData;
    }

    public function decrease(int $id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getFullCart() as $item) {
            $total += $item['produit']->getPrix() * $item['quantity'];
        }

        return $total;
    }

    public function getQuantity(Produit $produit): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        return $cart[$produit->getId()] ?? 0;
    }
}
