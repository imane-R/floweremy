<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/admin/categories', name: 'category_index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->render("category/index.html.twig", [
            'categories' => $categories
        ]);
    }

    #[Route('/admin/categories/new', name: 'category_new', methods: ['GET'])]
    public function new(): Response
    {
        $form = $this->createForm(CategoryFormType::class, null, [
            'action' => $this->generateUrl('category_create'),
            'method' => 'POST',
        ]);

        return $this->render("category/form.html.twig", [
            'formCategory' => $form->createView(),
        ]);
    }

    #[Route('/admin/categories', name: 'category_create', methods: ['POST'])]
    public function create(Request $request): Response
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

    #[Route('/admin/categories/{id}', name: 'category_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $products = $category->getProducts();

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }

    #[Route('/admin/categories/{id}/edit', name: 'category_edit_form', methods: ['GET'])]
    public function editForm(int $id): Response
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $form = $this->createForm(CategoryFormType::class, $category, [
            'action' => $this->generateUrl('category_update', ['id' => $id]),
            'method' => 'POST',
        ]);

        return $this->render("category/form.html.twig", [
            'formCategory' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/admin/categories/{id}', name: 'category_update', methods: ['POST'])]
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

    #[Route('/admin/categories/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $this->categoryService->deleteCategory($category);
        return $this->redirectToRoute('category_index');
    }


    public function dropdown(): Response
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->render('partials/category_menu.html.twig', [
            'categories' => $categories,
        ]);
    }
}
