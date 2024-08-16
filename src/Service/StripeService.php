<?php
// src/Service/StripeService.php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Produit;
use Stripe\StripeClient;
use Symfony\Bundle\MakerBundle\Str;

class StripeService
{
    private $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);
    }

    public function createPaymentSession(Commande $order, String $successUrl)

    {

        // build line items
        $lineItems = [];
        foreach ($order->getLigneCommandes() as $item) {
            $stripeProduct = $this->retreiveProduct($item->getProduit()->getStripeId());
            $lineItems[] = [
                'price' => $stripeProduct->default_price,
                'quantity' => $item->getQuantite()
            ];
        }

        return
            $this->stripe->checkout->sessions->create([
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'line_items' => $lineItems,
                'mode' => 'payment',
                'client_reference_id' => $order->getId(),
            ]);
    }

    public function getPaymentSession($id)
    {
        return $this->stripe->checkout->sessions->retrieve($id);
    }


    public function createProduct(Produit $produit)
    {
        return $this->stripe->products->create([
            'name' => $produit->getNomProduit(),
            'default_price_data' => [
                'currency' => $_ENV['SITE_CURRENCY'],
                'unit_amount_decimal' => $produit->getPrix() * 100
            ],
        ]);
    }

    public function retreiveProduct($id)
    {
        return $this->stripe->products->retrieve($id);
    }
}
