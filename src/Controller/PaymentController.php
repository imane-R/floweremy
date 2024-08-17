<?php
// src/Controller/PaymentController.php

namespace App\Controller;

use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    private $stripeService;
    private $orderService;

    public function __construct(StripeService $stripeService, OrderService $orderService)
    {
        $this->stripeService = $stripeService;
        $this->orderService = $orderService;
    }

    #[Route('/confirmation', name: 'payment_confirmation')]
    public function confirmation(): Response
    {
        $paymentSession = $this->stripeService->getPaymentSession($_GET['session_id']);

        $order = $this->orderService->getOrder($paymentSession->client_reference_id);
        $this->orderService->updateOrderStatus($order, $paymentSession->payment_status);
        return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
    }
}
