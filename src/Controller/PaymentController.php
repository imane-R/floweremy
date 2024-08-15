<?php
// src/Controller/PaymentController.php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(Request $request, StripeService $stripeService): Response
    {
        $amount = 100; // Montant de l'achat en dollars

        $paymentIntent = $stripeService->createPaymentIntent($amount);

        return $this->render('payment/index.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'publicKey' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }

    #[Route('/confirmation', name: 'payment_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('payment/confirmation.html.twig');
    }
}
