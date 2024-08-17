<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Service\ProductService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private ProductService $productService;
    private StripeService $stripeService;

    public function __construct(ProductService $productService, StripeService $stripeService)
    {
        $this->productService = $productService;
        $this->stripeService = $stripeService;
    }

    #[Route('/admin/products/new', name: 'product_new', methods: ['GET'])]
    public function new(): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product, [
            'action' => $this->generateUrl('product_create'),
            'method' => 'POST',
        ]);

        return $this->render("product/form.html.twig", [
            'formProduct' => $form->createView(),
        ]);
    }

    #[Route('/admin/products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageForm')->getData();
            $imagePath = $this->productService->uploadImage($product, $file, $this->getParameter('products_images'));

            if ($imagePath) {
                $stripeProduct = $this->stripeService->createProduct($product);
                $product->setStripeId($stripeProduct->id);
                $this->productService->saveProduct($product, $imagePath);
            }

            return $this->redirectToRoute('product_index');
        }

        return $this->render("product/form.html.twig", [
            'formProduct' => $form->createView(),
        ]);
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

    #[Route('/admin/products', name: 'product_index', methods: ['GET'])]
    public function index(): Response
    {
        $products = $this->productService->findAllProducts();

        return $this->render("product/index.html.twig", [
            'products' => $products
        ]);
    }

    #[Route('/admin/products/{id}/edit', name: 'product_edit_form', methods: ['GET'])]
    public function editForm(int $id): Response
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductFormType::class, $product, [
            'action' => $this->generateUrl('product_update', ['id' => $id]),
            'method' => 'POST',
        ]);

        return $this->render("product/form.html.twig", [
            'formProduct' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/admin/products/{id}', name: 'product_update', methods: ['POST'])]
    public function update(int $id, Request $request): Response
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageForm')->getData();
            $imagePath = $this->productService->uploadImage($product, $file, $this->getParameter('products_images'));

            $this->productService->saveProduct($product, $imagePath);

            return $this->redirectToRoute('product_index');
        }

        return $this->render("product/form.html.twig", [
            'formProduct' => $form->createView(),
            'product' => $product
        ]);
    }



    #[Route('/admin/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $product = $this->productService->findProductById($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $this->productService->deleteProduct($product);

        return $this->redirectToRoute('product_index');
    }

    public function tile(Product $product): Response
    {
        return $this->render('product/tile.html.twig', [
            'product' => $product // Pass the single product variable to the template
        ]);
    }
}
