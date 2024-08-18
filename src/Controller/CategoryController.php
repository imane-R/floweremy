<?php

namespace App\Controller;

use App\Service\CategoryService;
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
    #[Route('categories/{id}', name: 'category_show', methods: ['GET'])]
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

    public function dropdown(): Response
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->render('partials/category_menu.html.twig', [
            'categories' => $categories,
        ]);
    }
}
