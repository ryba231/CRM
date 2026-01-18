<?php
namespace App\Controller;

use App\Service\User\UserDeleter;
use App\Service\User\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1')]
class UserController extends AbstractController
{
    public function __construct(
        private UserDeleter $deleter
    ){}

    #[Route('/me', name: 'app_me', methods:['GET'])]
    public function me(
        UserManager $userManager
        ) : JsonResponse {
            return $this->json($userManager->getCurrentUser());
    }

    #[Route('/user', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(
    ) : JsonResponse {
        $this->deleter->delete(
            $this->getUser(),
            $this->getUser()
        );

        return $this->json(null,204);
    }
}
