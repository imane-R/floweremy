<?php
// src/Service/OrderService.php
namespace App\Service;

use App\Entity\Order;
use App\Entity\ProductLine;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;

class OrderService
{
    private OrderRepository $orderRepository;
    private CartService $cartService;
    private Security $security;
    private ProductRepository $productRepository;

    public function __construct(OrderRepository $orderRepository, CartService $cartService, Security $security, ProductRepository $productRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
        $this->security = $security;
        $this->productRepository = $productRepository;
    }

    public function createOrder(?Order $order = null): Order
    {
        $cart = $this->cartService->getFullCart();

        if (empty($cart)) {
            throw new \InvalidArgumentException('Cart is empty, cannot create an order.');
        }

        if ($order === null) {
            $order = new Order();
        }

        $order->setCreationDate(new \DateTime());
        $order->setStatus(OrderStatus::PENDING);
        $order->setTotalPrice($this->cartService->getTotal());
        $order->setUser($this->security->getUser());

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

    public function getOrder(int $orderId): Order
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new \InvalidArgumentException("Order with ID $orderId not found.");
        }
        return $order;
    }

    public function updateOrderStatus(Order $order, OrderStatus $status): void
    {
        if ($order->getStatus() === $status) {
            throw new \InvalidArgumentException('Order already has status ' . $status);
        }

        $order->setStatus($status);

        // If the order is confirmed, decrease stock
        if ($status === OrderStatus::CONFIRMED) {
            $this->decreaseStock($order);
        }

        $this->orderRepository->save($order, true);
    }

    private function decreaseStock(Order $order): void
    {
        foreach ($order->getProductLines() as $productLine) {
            $product = $productLine->getProduct();
            $newStock = $product->getStock() - $productLine->getQuantity();

            if ($newStock < 0) {
                throw new \Exception('Not enough stock for product: ' . $product->getName());
            }

            $product->setStock($newStock);
        }

        // Save changes to the database
        $this->productRepository->save($product);
    }
}
