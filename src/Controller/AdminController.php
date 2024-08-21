<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\OrderFormType;
use App\Form\ProductFormType;
use App\Service\OrderService;
use App\Entity\LegalAndPolicy;
use App\Form\CategoryFormType;
use App\Service\StripeService;
use App\Service\ProductService;
use App\Form\LegalAndPolicyType;
use App\Service\CategoryService;
use App\Service\LegalAndPolicyService;
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
    private OrderService $orderService;
    private LegalAndPolicyService $legalAndPolicyService;

    public function __construct(CategoryService $categoryService, ProductService $productService, StripeService $stripeService, OrderService $orderService, LegalAndPolicyService $legalAndPolicyService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->stripeService = $stripeService;
        $this->orderService = $orderService;
        $this->legalAndPolicyService = $legalAndPolicyService;
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

    #[Route('/products/synchronize', name: 'product_synchronize', methods: ['POST'])]
    public function synchronizeProducts(): Response
    {
        $products = $this->productService->findAllProducts();

        foreach ($products as $product) {
            if (!$product->getStripeId()) {
                $stripeProduct = $this->stripeService->createProduct($product);
                $product->setStripeId($stripeProduct->id);
                $this->productService->saveProduct($product);
            } else {
                try {
                    $stripeProduct = $this->stripeService->retreiveProduct($product->getStripeId());
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $stripeProduct = $this->stripeService->createProduct($product);
                    $product->setStripeId($stripeProduct->id);
                    $this->productService->saveProduct($product);
                }
            }
        }

        $this->addFlash('success', 'Produits synchronisés avec Stripe avec succès.');

        return $this->redirectToRoute('product_index');
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

    // legal and policy management

    #[Route('/edit', name: 'legal_and_policy_edit')]
    public function edit(Request $request): Response
    {
        $legalAndPolicy = $this->legalAndPolicyService->getLegalAndPolicy();
        if (!$legalAndPolicy) {
            $legalAndPolicy = new LegalAndPolicy();
        }

        $form = $this->createForm(LegalAndPolicyType::class, $legalAndPolicy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->legalAndPolicyService->saveLegalAndPolicy($legalAndPolicy);
            return $this->redirectToRoute('legal_and_policy_show');
        }

        return $this->render('legal_and_policy/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show_legal_and_policy', name: 'legal_and_policy_show')]
    public function showLegalAndPolicy(): Response
    {
        $legalAndPolicy = $this->legalAndPolicyService->getLegalAndPolicy();
        return $this->render('legal_and_policy/show.html.twig', [
            'legalAndPolicy' => $legalAndPolicy,
        ]);
    }

    // oders management

    #[Route('/orders', name: 'order_index', methods: ['GET'])]
    public function orderindex(): Response
    {
        $orders = $this->orderService->getAllOrders();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'order_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $order = $this->orderService->getOrder($id);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/update', name: 'update_order', methods: ['POST', 'GET'])]
    public function updateOrder(int $id, Request $request): Response
    {
        $order = $this->orderService->getOrder($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'objet `Order` avec les données du formulaire
            $this->orderService->updateOrder($order);
            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/form.html.twig', [
            'formOrder' => $form->createView(),
            'order' => $order,
        ]);
    }
}
