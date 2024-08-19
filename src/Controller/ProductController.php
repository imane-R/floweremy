<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private ProductService $productService;


    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $relatedProducts = $this->productService->findRelatedProducts($product);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    #[Route('/search/products', name: 'product_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');
        $products = $this->productService->searchProducts($query);

        return $this->render('product/search_results.html.twig', [
            'products' => $products,
            'query' => $query
        ]);
    }

    #[Route('/products', name: 'products', methods: ['GET'])]

    public function findAll(): Response
    {
        $products = $this->productService->findAllProducts();

        return $this->render('product/products.html.twig', [
            'products' => $products
        ]);
    }

    // This method is used to render a single product tile
    public function tile(Product $product): Response
    {
        return $this->render('product/tile.html.twig', [
            'product' => $product // Pass the single product variable to the template
        ]);
    }
}
