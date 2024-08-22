<?php

namespace App\Controller;

use App\Service\UserService;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

    #[Route('/profile/change-password', name: 'profile_change_password')]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            try {
                $this->userService->changePassword($user, $currentPassword, $newPassword, $confirmPassword);
                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                return $this->redirectToRoute('profile');
            } catch (AuthenticationException $e) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur s\'est produite lors du changement du mot de passe.');
            }
        }

        return $this->render('profile/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}
