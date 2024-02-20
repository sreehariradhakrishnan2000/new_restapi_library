<?php
// tests/Controller/SecurityControllerTest.php

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends TestCase
{
    private SecurityController $securityController;
    private UserService $userService;
    private JWTTokenManagerInterface $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock dependencies
        $this->userService = $this->createMock(UserService::class);
        $this->tokenManager = $this->createMock(JWTTokenManagerInterface::class);

        // Create instance of SecurityController with mocked dependencies
        $this->securityController = new SecurityController($this->userService);
    }

    public function testLoginWithValidCredentials(): void
    {
        // Arrange
        $request = new Request([], [], [], [], [], [], '{"email": "test@example.com", "password": "password"}');
        $user = (object) ['email' => 'test@example.com', 'password' => password_hash('password', PASSWORD_DEFAULT)]; // Define a user object for successful authentication
        
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with('test@example.com')
            ->willReturn($user);
        
        $this->userService->expects($this->once())
            ->method('validateUserPassword')
            ->with($user, 'password')
            ->willReturn(true);

        $this->tokenManager->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('mocked_jwt_token');

        // Act
        $response = $this->securityController->login($request, $this->tokenManager);

        // Assert
        $this->assertEquals('mocked_jwt_token', $response->getContent());
    }

    public function testLoginWithInvalidCredentials(): void
    {
        // Arrange
        $request = new Request([], [], [], [], [], [], '{"email": "test@example.com", "password": "password"}');
        $user = null; // No user found for provided email
        
        $this->userService->expects($this->once())
            ->method('getUserByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        // Act
        $response = $this->securityController->login($request, $this->tokenManager);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    // Add more test methods to cover other scenarios (missing credentials, edge cases, etc.)
}
