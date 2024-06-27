<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

readonly class ApiSecurityController implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private JWTTokenManagerInterface $JWTTokenManager)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $data = ['error' => 'Identifiants incorrects'];
            $statusCode = Response::HTTP_UNAUTHORIZED;
        } elseif (!$user->isApiActivated()) {
            $data = ['error' => 'Accès API non activé'];
            $statusCode = Response::HTTP_FORBIDDEN;
        } else {
            $jwtToken = $this->JWTTokenManager->create($user);

            $data = ['token' => $jwtToken];
            $statusCode = Response::HTTP_OK;
        }

        return new JsonResponse($data, $statusCode);
    }
}
