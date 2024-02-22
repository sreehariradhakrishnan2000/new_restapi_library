<?php
namespace App\Tests\Controller;

use App\Controller\SecurityController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Entity\User;

class SecurityControllerTest extends TestCase
{
    public function testLogin(): void
    {
        $userService = $this->createMock(UserService::class);
        $userService->expects($this->once())
                    ->method('getUserByEmail')
                    ->willReturn(new User());

        $userService->expects($this->once())
                    ->method('validateUserPassword')
                    ->willReturn(true);

        $tokenManager = $this->createMock(JWTTokenManagerInterface::class);
        $tokenManager->expects($this->once())
                     ->method('create')
                     ->willReturn('mock_token');

        $request = new Request([], [], [], [], [], [], json_encode([
            'email' => 'rekhaa@gmail.com',
            'password' => 'Rekha@123'
        ]));

        $controller = new SecurityController($userService);

        $response = $controller->login($request, $tokenManager);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals('mock_token', $response->getContent());
    }
}
?>