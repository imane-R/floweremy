<?php
// src/Service/StripeService.php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    private $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createPaymentIntent(float $amount, string $currency = 'EUR')
    {
        return $this->stripe->paymentIntents->create([
            'amount' => $amount * 100, // Le montant est en centimes
            'currency' => $currency,
        ]);
    }
}
