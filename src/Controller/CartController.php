<?php
// src/Controller/CartController.php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/cart', name: 'app-cart')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getFullCart(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id): Response
    {
        $this->cartService->add($id);

        return $this->redirectToRoute('app-cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id): Response
    {
        $this->cartService->remove($id);

        return $this->redirectToRoute('app-cart');
    }

    #[Route('/cart/increase/{id}', name: 'cart_increase', methods: ['POST'])]
    public function increase(int $id): Response
    {
        $this->cartService->add($id);

        return $this->redirectToRoute('app-cart');
    }

    #[Route('/cart/decrease/{id}', name: 'cart_decrease', methods: ['POST'])]
    public function decrease(int $id): Response
    {
        $this->cartService->decrease($id); // Implémentez cette méthode dans CartService pour réduire la quantité

        return $this->redirectToRoute('app-cart');
    }
}
