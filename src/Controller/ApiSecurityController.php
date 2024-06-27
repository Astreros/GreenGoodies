<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ApiSecurityController extends AbstractController
{
    public function __construct(private readonly AuthenticationUtils $authenticationUtils,
                                private readonly JWTTokenManagerInterface $JWTTokenManager)
    {
    }

    #[Route('/api/login', name: 'api.login', methods: ['POST'])]
    public function index(): JsonResponse
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        if ($error) {
            return new JsonResponse(['error' => 'Invalid credentials.'], 401);
        }

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createNotFoundException('User not found');
        }

        $token = $this->JWTTokenManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ]);
    }
}
