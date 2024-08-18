<?php
// src/Service/OrderService.php
namespace App\Service;

use App\Entity\Order;
use App\Entity\ProductLine;
use App\Repository\OrderRepository;
use Symfony\Bundle\SecurityBundle\Security;

class  OrderService
{
    private $orderRepository;
    private $cartService;
    private $security;

    public function __construct(OrderRepository $orderRepository, CartService $cartService, Security $security)
    {
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
        $this->security = $security;
    }

    public function createOrder(?Order $order): Order
    {
        $cart = $this->cartService->getFullCart();

        if (empty($order)) {
            $order = new Order();
        }
        $order->setCreationDate(new \DateTime());
        $order->setStatus('pending');
        $order->setTotalPrice($this->cartService->getTotal());
        $order->setUser($this->security->getUser());

        // Add each product to the ligneCommandes of the order 
        foreach ($cart as $item) {
            $product = $item['product'];
            $orderLine = new ProductLine();
            $orderLine->setProduct($product);
            $orderLine->setQuantity($item['quantity']);
            $orderLine->setOrder($order);
            $order->addProductLine($orderLine);
        }

        $this->orderRepository->save($order, true);
        $this->cartService->removeCart();
        return $order;
    }

    public function getOrder($orderId): Order
    {
        return $this->orderRepository->find($orderId);
    }

    public function updateOrderStatus(Order $order, string $status): void
    {
        $order->setStatus($status);
        $this->orderRepository->save($order, true);
    }
}
