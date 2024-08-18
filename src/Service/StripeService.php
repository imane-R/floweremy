<?php
// src/Service/StripeService.php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use Stripe\StripeClient;

class StripeService
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
    }

    public function createPaymentSession(Order $order, String $successUrl, String $cancelUrl)

    {

        // build line items
        $lineItems = [];
        foreach ($order->getProductLines() as $item) {
            $stripeProduct = $this->retreiveProduct($item->getProduct()->getStripeId());
            $lineItems[] = [
                'price' => $stripeProduct->default_price,
                'quantity' => $item->getQuantity()
            ];
        }

        return
            $this->stripe->checkout->sessions->create([
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'line_items' => $lineItems,
                'mode' => 'payment',
                'client_reference_id' => $order->getId(),
            ]);
    }

    public function getPaymentSession($id)
    {
        return $this->stripe->checkout->sessions->retrieve($id);
    }


    public function createProduct(Product $product)
    {
        return $this->stripe->products->create([
            'name' => $product->getName(),
            'default_price_data' => [
                'currency' => $_ENV['SITE_CURRENCY'],
                'unit_amount_decimal' => $product->getPrice() * 100
            ],
        ]);
    }

    public function retreiveProduct($id)
    {
        return $this->stripe->products->retrieve($id);
    }
}
