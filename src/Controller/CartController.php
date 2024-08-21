<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cartService;
    private $productService;

    public function __construct(CartService $cartService, ProductService $productService)
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
    }

    #[Route('/cart', name: 'cart_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cartService->getFullCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    #[Route('/cart/items', name: 'cart_add_item', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $productId = $request->request->get('productId');
        if ($productId) {
            $product = $this->productService->findProductById($productId);
            $currentQuantityInCart = $this->cartService->getQuantity($product);

            // Check if the stock is sufficient
            if ($product->getStock() > $currentQuantityInCart) {
                $this->cartService->add($productId);
            } else {
                // If stock is insufficient, add a flash message
                $this->addFlash('warning', 'Stock insuffisant pour ' . $product->getName());
            }
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/items/{id}', name: 'cart_remove_item', methods: ['DELETE'])]
    public function remove(int $id): Response
    {
        $this->cartService->remove($id);

        // Return a JSON response with success status and redirect URL
        return $this->json([
            'success' => true,
            'redirect_url' => $this->generateUrl('cart_index'),
        ]);
    }

    #[Route('/cart/items/{id}/increase', name: 'cart_increase_quantity', methods: ['POST'])]
    public function increase(int $id): Response
    {
        $product = $this->productService->findProductById($id);
        $currentQuantityInCart = $this->cartService->getQuantity($product);

        // Check if the stock is sufficient
        if ($product->getStock() > $currentQuantityInCart) {
            $this->cartService->increaseQuantity($id);
        } else {
            $this->addFlash('warning', 'IStock insuffisant pour ' . $product->getName());
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/items/{id}/decrease', name: 'cart_decrease_quantity', methods: ['POST'])]
    public function decrease(int $id): Response
    {
        $this->cartService->decreaseQuantity($id);

        return $this->redirectToRoute('cart_index');
    }

    public function addToCartButton(Product $product): Response
    {
        return $this->render('cart/add_to_cart_button.html.twig', [
            'product' => $product,
            'maxQuantity' => $product->getStock() - $this->cartService->getQuantity($product),
        ]);
    }
}
