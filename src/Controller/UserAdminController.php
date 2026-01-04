<?php
namespace App\Controller;

use App\DTO\User\RegisterUserDTO;
use App\Entity\User\User;
use App\Mapper\User\UserMapper;
use App\Service\User\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/admin/users')]
class UserAdminController extends AbstractController
{
    public function __construct(private UserManager $userManager) {}

    #[Route(methods:['GET'], name: 'app_users')]
    public function list(
        Request $request
        ): JsonResponse {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
            
            $page = max(1, $request->query->getInt('page', 1));
            $limit = min(50, max(1, $request->query->getInt('limit', 10)));
            try {
                $result = $this->userManager->getAllUsers($page, $limit);
                $usersDto = array_map(
                    fn(User $user) => UserMapper::toDTO($user), 
                    $result['items']
                );
            } catch(\DomainException $e) {
                return $this->json(['error' => $e->getMessage()], 404);
            }

            return $this->json(
                [
                    'data' => $usersDto,
                    'meta' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $result['total'],
                        'pages' => (int) ceil($result['total'] / $limit),
                    ]
                ], 
                200);
    }

    #[Route('/{id}', methods:['GET'], name: 'app_user_get')]
    public function get(
        int $id
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json(
            UserMapper::toDTO($this->userManager->get($id))
        );
    }

    #[Route('', methods:['POST'], name: 'app_users_create')]
    public function create(
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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
            $user = $this->userManager->createUser($dto);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

        return $this->json(
            UserMapper::toDTO($user),
            201
        );
    }

    #[Route('/{id}', methods:['PATCH'], name: 'app_user_update')]
    public function update(
        int $id,
        Request $request
    ) : JsonResponse {
         $this->denyAccessUnlessGranted('ROLE_ADMIN');
         $data = json_decode($request->getContent(), true);

         $user = $this->userManager->get($id);

         $this->userManager->update(
            $user,
            $data['email'] ?? null,  
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['password'] ?? null
         );

         return $this->json(
            UserMapper::toDTO($user)
        );
    }

    #[Route('/{id}', methods:['DELETE'], name: 'app_user_delete')]
    public function delete(
        int $id
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->userManager->delete(
            $this->userManager->get($id) 
        );

        return $this->json(null,204);
    }
}