<?php
// src/Service/OrderService.php
namespace App\Service;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class  OrderService
{
    private $commandeRepository;
    private $cartService;
    private $user;

    public function __construct(CommandeRepository $commandeRepository, CartService $cartService)
    {
        $this->commandeRepository = $commandeRepository;
        $this->cartService = $cartService;
    }

    public function createOrder(): Commande
    {
        $cart = $this->cartService->getFullCart();
        $order = new Commande();
        $order->setDateCommande(new \DateTime());
        $order->setDateLivraison(new \DateTime());
        $order->setStatutCode('pending');
        $order->setTotalCommande($this->cartService->getTotal());
        //$order->setUserId('1');

        // Add each product to the ligneCommandes of the order 
        foreach ($cart as $item) {
            $product = $item['produit'];
            $orderLine = new LigneCommande();
            $orderLine->setProduit($product);
            $orderLine->setQuantite($item['quantity']);
            $orderLine->setCommande($order);
            $order->addLigneCommande($orderLine);
        }

        $this->commandeRepository->save($order, true);
        return $order;
    }

    public function getOrder($orderId): Commande
    {
        return $this->commandeRepository->find($orderId);
    }

    public function updateOrderStatus(Commande $order, string $status): void
    {
        $order->setStatutCode($status);
        $this->commandeRepository->save($order, true);
    }
}
