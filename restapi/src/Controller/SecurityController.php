<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\DTO\AuthDto; // Corrected import statement
use App\Service\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User; // Add this line to import User entity

class SecurityController extends AbstractController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $authDto = new AuthDto();
        $authDto->setUsername($data['username'] ?? '');
        $authDto->setPassword($data['password'] ?? '');

        try {
            // Authenticate user and generate JWT token
            $token = $this->authService->authenticate($authDto);

            return new JsonResponse(['token' => $token]);
        } catch (BadCredentialsException $e) { 
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }
}
