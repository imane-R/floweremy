<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->registerUser(
                $user,
                $form->get('plainPassword')->getData()
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin_register', name: 'app_register_admin')]
    public function registerAdmin(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->registerUser(
                $user,
                $form->get('plainPassword')->getData(),
                ['ROLE_ADMIN']
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin_all_admins', name: 'admins')]
    public function showAllAdmins(): Response
    {
        $users = $this->userService->getAllAdmins();

        return $this->render('registration/showAllAdmins.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin_update/{id}', name: 'update_admin')]
    public function editAdmin(User $user, Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->registerUser(
                $user,
                $form->get('plainPassword')->getData(),
                $user->getRoles()
            );

            return $this->redirectToRoute('admins');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin_delete/{id}', name: 'delete_admin')]
    public function deleteAdmin(User $user): Response
    {
        $this->userService->deleteUser($user);

        return $this->redirectToRoute('admins');
    }
}
