<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function saveProduct(Product $product, ?string $imagePath = null): void
    {
        if ($imagePath) {
            $product->setImage($imagePath);
        }
        $this->productRepository->save($product);
    }

    public function deleteProductById(int $productId): void
    {
        $product = $this->findProductById($productId);
        $this->productRepository->remove($product);
    }

    public function uploadImage(Product $product, $file, string $uploadDir): ?string
    {
        if ($file) {
            $fileName = $product->getName() . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($uploadDir, $fileName);
                return $fileName;
            } catch (FileException $e) {
                // Log error or handle it as needed
                return null;
            }
        }
        return null;
    }

    public function findProductById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function findAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function searchProducts(string $query): array
    {
        return $this->productRepository->search($query);
    }

    public function findRelatedProducts(Product $product, int $limit = 4): array
    {
        return $this->productRepository->findBy([], null, $limit);
    }

    public function getBestSellers(int $limit = 4): array
    {
        return $this->productRepository->findBestSellers($limit);
    }
}
