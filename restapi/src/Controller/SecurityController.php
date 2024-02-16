<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Service\UserService;

class SecurityController extends AbstractController
{

    public function __construct(
        private UserService $userService
    ){
    }

    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $tokenManager): Response
    {
        $userCreds = json_decode($request->getContent());

        if (($userCreds->email ?? null) === null || ($userCreds->password ?? null) === null) {
            throw new  \Exception('Missing user credentials');
        }
    
        $user = $this->userService->getUserByEmail($userCreds->email);
        
        if ($user === null) {
            return new Response(
                sprintf('No user exists for email %s', $userCreds->email), 
                Response::HTTP_UNAUTHORIZED
            );
        }
    
        if (!$this->userService->validateUserPassword($user, $userCreds->password)) {
            return new Response('Authentication failed because password is not correct', Response::HTTP_UNAUTHORIZED);
        }
    
        $token = $tokenManager->create($user);
    
        return new Response($token);
        
    }
}