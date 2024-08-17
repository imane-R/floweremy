<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $ProductRepository): Response
    {
        $products = $ProductRepository->findAll();

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
