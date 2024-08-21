<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $userRepository;
    private $entityManager;
    private $passwordHasher;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerUser(User $user, string $plainPassword, array $roles = ['ROLE_USER']): void
    {
        $user->setRoles($roles);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getAllAdmins(): array
    {
        return $this->userRepository->findByRole('ROLE_ADMIN');
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    // Add more methods as needed...
}
