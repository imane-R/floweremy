<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    public function updateCategory(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    public function deleteCategoryById(int $categoryId): void
    {
        $category = $this->getCategoryById($categoryId);
        $this->categoryRepository->remove($category);
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }
}
