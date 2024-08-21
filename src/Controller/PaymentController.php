<?php
// src/Controller/PaymentController.php

namespace App\Controller;

use App\Enum\OrderStatus;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private $stripeService;
    private $orderService;
    private $cartService;

    public function __construct(StripeService $stripeService, OrderService $orderService, CartService $cartService)
    {
        $this->stripeService = $stripeService;
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    #[Route('/payment/confirm', name: 'payment_confirm')]
    public function confirmation(): Response
    {
        $paymentSession = $this->stripeService->getPaymentSession($_GET['session_id']);

        $order = $this->orderService->getOrder($paymentSession->client_reference_id);
        if ($paymentSession->payment_status === 'paid') {
            $this->orderService->updateOrderStatus($order, OrderStatus::CONFIRMED);
        }
        return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        $paymentSession = $this->stripeService->getPaymentSession($_GET['session_id']);

        $order = $this->orderService->getOrder($paymentSession->client_reference_id);

        $this->orderService->updateOrderStatus($order, OrderStatus::CANCELLED);

        $this->cartService->recoverCartFromOrder($order);

        $this->addFlash('warning', 'Le paiement a été annulé.');
        return $this->redirectToRoute('cart_index');
    }
}
