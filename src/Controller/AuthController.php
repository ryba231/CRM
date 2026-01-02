<?php

namespace App\Controller;

use App\DTO\User\RegisterUserDTO;
use App\Service\User\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'app_register')]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        UserManager $userManager,
        JWTTokenManagerInterface $JWTTokenManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new RegisterUserDTO();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->password = $data['password'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], 400);
        }

        try {
            $user = $userManager->createUser($dto);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

        $token = $JWTTokenManager->create($user);
        return $this->json([
            'token' => $token,
            'user' => [
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ]
        ], 201);
    }

    #[Route('/api/login', name: 'app_login')]
    public function login()
    {}
}
