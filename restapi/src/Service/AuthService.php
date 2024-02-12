<?php
// src/Service/AuthService.php

namespace App\Service;

use App\DTO\AuthDto; // Corrected import statement
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AuthService
{
    private $userRepository;
    private $JWTManager;

    // Inject the default value for PUBLIC_KEY_PATH here
    private $publicKeyPath;

    public function __construct(UserRepository $userRepository, JWTTokenManagerInterface $JWTManager, string $publicKeyPath)
    {
        $this->userRepository = $userRepository;
        $this->JWTManager = $JWTManager;
        $this->publicKeyPath = $publicKeyPath;
    }

    public function authenticate(AuthDto $authDto): string // Corrected class name
    {
        $username = $authDto->getUsername();
        $password = $authDto->getPassword();

        // Validate username and password
        if (!$username || !$password) {
            throw new BadCredentialsException('Missing username or password.');
        }

        // Implement logic to authenticate user (e.g., query user from database)
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new BadCredentialsException('Invalid username or password.');
        }

        // Generate JWT token
        return $this->JWTManager->create($user);
    }
}
