<?php
// src/Controller/PaymentController.php

namespace App\Controller;

use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/payment', name: 'payment')]
    public function index(Request $request): Response
    {
        $successLink = $this->generateUrl('payment_confirmation', [], 0);
        // order serverci  pou  creat order avec status pending et à la place de carte service on envoir prder service
        $order = $this->orderService->createOrder();
        $paymentSession =  $this->stripeService->createPaymentSession($order, $successLink);

        return $this->render('payment/index.html.twig', [
            'url' => $paymentSession->url
        ]);
    }

    #[Route('/confirmation', name: 'payment_confirmation')]
    public function confirmation(): Response
    {
        $paymentSession = $this->stripeService->getPaymentSession($_GET['session_id']);

        $order = $this->orderService->getOrder($paymentSession->client_reference_id);
        $this->orderService->updateOrderStatus($order, $paymentSession->payment_status);
        return $this->render('payment/confirmation.html.twig', [
            'paymentSession' => $paymentSession
        ]);
    }
}
