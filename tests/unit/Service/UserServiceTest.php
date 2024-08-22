<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private MockObject $userRepository;
    private MockObject $entityManager;
    private MockObject $passwordHasher;
    
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService(
            $this->userRepository,
            $this->entityManager,
            $this->passwordHasher
        );
    }

    public function testRegisterUser(): void
    {
        $user = new User();
        $plainPassword = 'plain_password';
        $hashedPassword = 'hashed_password';
        
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $plainPassword)
            ->willReturn($hashedPassword);
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($user);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $user->setRoles(['ROLE_USER']);
        
        $this->userService->registerUser($user, $plainPassword);
        
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals($hashedPassword, $user->getPassword());
    }

    public function testGetAllAdmins(): void
    {
        $adminUser = new User();
        $adminUser->setRoles(['ROLE_ADMIN']);
        
        $this->userRepository->expects($this->once())
            ->method('findByRole')
            ->with('ROLE_ADMIN')
            ->willReturn([$adminUser]);
        
        $admins = $this->userService->getAllAdmins();
        
        $this->assertCount(1, $admins);
        $this->assertSame($adminUser, $admins[0]);
    }

    public function testDeleteUser(): void
    {
        $user = new User();
        
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($user);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $this->userService->deleteUser($user);
    }

    public function testChangePasswordSuccess(): void
    {
        $user = new User();
        $currentPassword = 'current_password';
        $newPassword = 'new_password';
        $confirmPassword = 'new_password';
        $hashedPassword = 'hashed_new_password';
        
        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $currentPassword)
            ->willReturn(true);
        
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($user, $newPassword)
            ->willReturn($hashedPassword);
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $this->userService->changePassword($user, $currentPassword, $newPassword, $confirmPassword);
        
        $this->assertEquals($hashedPassword, $user->getPassword());
    }

    public function testChangePasswordFailsOnWrongCurrentPassword(): void
    {
        $user = new User();
        $currentPassword = 'wrong_current_password';
        $newPassword = 'new_password';
        $confirmPassword = 'new_password';
        
        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $currentPassword)
            ->willReturn(false);
        
        $this->expectException(AuthenticationException::class);
        
        $this->userService->changePassword($user, $currentPassword, $newPassword, $confirmPassword);
    }

    public function testChangePasswordFailsOnPasswordMismatch(): void
    {
        $user = new User();
        $currentPassword = 'current_password';
        $newPassword = 'new_password';
        $confirmPassword = 'different_new_password';
        
        $this->passwordHasher->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $currentPassword)
            ->willReturn(true);
        
        $this->expectException(\InvalidArgumentException::class);
        
        $this->userService->changePassword($user, $currentPassword, $newPassword, $confirmPassword);
    }
}
