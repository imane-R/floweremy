<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $products = $this->productService->getBestSellers();
        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/about-us', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }
}
