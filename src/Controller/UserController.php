<?php
namespace App\Controller;

use App\Service\User\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/api/v1/me', name: 'app_me', methods:['GET'])]
    public function me(
        UserManager $userManager
        ) : JsonResponse {
            return $this->json($userManager->getCurrentUser());
    }
}
