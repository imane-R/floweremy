<?php

namespace App\Controller;

use App\Entity\Mletpc;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\MletpcType;
use App\Form\ProductFormType;
use App\Form\CategoryFormType;
use App\Service\MletPcService;
use App\Service\StripeService;
use App\Service\ProductService;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




// admine route for admin actions
#[Route('/admin', name: '')]
class AdminController extends AbstractController
{
    private CategoryService $categoryService;
    private ProductService $productService;
    private StripeService $stripeService;
    private MletPcService $mletpcService;

    public function __construct(CategoryService $categoryService, ProductService $productService, StripeService $stripeService, MletPcService $mletpcService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->stripeService = $stripeService;
        $this->mletpcService = $mletpcService;
    }

    // gestion of category

    #[Route('/categories', name: 'category_index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->render("category/index.html.twig", [
            'categories' => $categories
        ]);
    }

    #[Route('/categories/new', name: 'category_new', methods: ['GET'])]
    public function newCategory(): Response
    {
        $form = $this->createForm(CategoryFormType::class, null, [
            'action' => $this->generateUrl('category_create'),
            'method' => 'POST',
        ]);

        return $this->render("category/form.html.twig", [
            'formCategory' => $form->createView(),
        ]);
    }

    #[Route('/categories', name: 'category_create', methods: ['POST'])]
    public function createCategories(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->createCategory($category);
            return $this->redirectToRoute('category_index');
        }

        return $this->render("category/form.html.twig", [
            'formCategory' => $form->createView(),
        ]);
    }

    #[Route('categories/{id}', name: 'category_update', methods: ['GET', 'POST'])]
    public function update(int $id, Request $request): Response
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->updateCategory($category);
            return $this->redirectToRoute('category_index');
        }

        return $this->render("category/form.html.twig", [
            'formCategory' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/categories/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {

        $this->categoryService->deleteCategoryById($id);

        // Return a JSON response with success status and redirect URL
        return $this->json([
            'success' => true,
            'redirect_url' => $this->generateUrl('category_index'),
        ]);
    }

    // gestion of product

    #[Route('/new', name: 'product_new', methods: ['GET'])]
    public function newProduct(): Response
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

    #[Route('/products', name: 'product_create', methods: ['POST'])]
    public function createProduct(Request $request): Response
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

    #[Route('/products', name: 'product_index', methods: ['GET'])]
    public function indexProduct(): Response
    {
        $products = $this->productService->findAllProducts();

        return $this->render("product/index.html.twig", [
            'products' => $products
        ]);
    }

    #[Route('products/{id}', name: 'product_update', methods: ['POST', 'GET'])]
    public function updateProduct(int $id, Request $request): Response
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



    #[Route('/products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function deleteProduct(int $id): Response
    {

        $this->productService->deleteProductById($id);

        // Return a JSON response with success status and redirect URL
        return $this->json([
            'success' => true,
            'redirect_url' => $this->generateUrl('product_index'),
        ]);
    }

    // gestion de ml et pc

    #[Route('/mletpc_update/{id}', name: 'mletpc_update', methods: ['GET', 'POST'])]
    public function mletpcupdate(int $id, Request $request): Response
    {
        $mletpc = $this->mletpcService->getMletpcById($id);

        if (!$mletpc) {
            throw $this->createNotFoundException('Mletpc not found');
        }

        $form = $this->createForm(MletpcType::class, $mletpc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mletpcService->updateMletpc($mletpc);
            return $this->redirectToRoute('mletpc_update');
        }

        return $this->render("mletpc/index.html.twig", [
            'formMletpc' => $form->createView(),
            'mletpc' => $mletpc
        ]);
    }
}
