<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\CheckoutFormType;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    private $cartService;
    private $orderService;
    private $stripeService;

    public function __construct(CartService $cartService, OrderService $orderService, StripeService $stripeService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
    }

    #[Route('/checkout', name: 'checkout', methods: ['GET', 'POST'])]
    public function checkout(Request $request): Response
    {
        // Check if the cart is empty
        $cartItems = $this->cartService->getFullCart();
        if (empty($cartItems)) {
            $this->addFlash('warning', 'Your cart is empty. Please add items to your cart before proceeding to checkout.');
            return $this->redirectToRoute('cart_index');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['product']->getStock()) {
                $this->addFlash('warning', sprintf('The product "%s" does not have enough stock. Available quantity: %d.', $item['product']->getName(), $item['product']->getStock()));
                return $this->redirectToRoute('cart_index');
            }
        }

        $order = new Order();

        // Create the form for checkout
        $form = $this->createForm(CheckoutFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $successLink = $this->generateUrl('payment_confirmation', [], 0);

            // Create the order with status 'Pending'
            $order = $this->orderService->createOrder($order);

            // Create the payment session with Stripe
            $paymentSession = $this->stripeService->createPaymentSession($order, $successLink);


            // Redirect to the payment session
            return $this->redirect($paymentSession->url);
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'items' => $this->cartService->getFullCart(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    #[Route('/order/confirmation/{id}', name: 'order_confirmation', methods: ['GET'])]
    public function orderConfirmation(Order $order): Response
    {
        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order
        ]);
    }
}
