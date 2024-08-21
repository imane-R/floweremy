<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();

        // // Fetch the orders of the logged-in user
        // $orders = $user->getOrders();

        return $this->render('profile/index.html.twig', [
            'user' => $user
        ]);
    }
}
