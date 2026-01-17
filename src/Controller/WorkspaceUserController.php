<?php

namespace App\Controller;

use App\DTO\Workspace\AddUserToWorkspaceDTO;
use App\DTO\Workspace\UpdateUserInWorkspaceDTO;
use App\Entity\Workspace\WorkspaceUser;
use App\Mapper\Workspace\WorkspaceUserMapper;
use App\Security\Enum\PermissionType;
use App\Service\Workspace\WorkspaceContextInterface;
use App\Service\Workspace\WorkspaceManager;
use App\Service\Workspace\WorkspaceUserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/workspace-user')]
final class WorkspaceUserController extends BaseApiController
{
    public function __construct(
        private WorkspaceManager $workspaceManager,
        private WorkspaceUserManager $workspaceUserManager,
        private WorkspaceContextInterface $workspaceContext
    ){}

    #[Route('', name: 'app_workspace_user_get', methods: ['GET'])]
    public function list(
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(
            PermissionType::USER_VIEW->value
        );

        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, max(1, $request->query->getInt('limit', 10)));
        $workspace = $this->workspaceContext->getCurrentWorkspace();

        $result = $this->workspaceManager->getAllUser($page, $limit, $workspace);

        $userDto = array_map(
            fn(WorkspaceUser $workspaceUser) => WorkspaceUserMapper::userToDTO($workspaceUser),
            $result['items']
        );
        
        return $this->json(
            [
                'data' => $userDto,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $result['total'],
                    'pages' => (int) ceil($result['total'] / $limit),
                ]
            ]
        );
    }

    #[Route('', name: 'app_workspace_user_add', methods: ['POST'])]
    public function add(
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse{
        $workspace = $this->workspaceContext->getCurrentWorkspace();

        $this->denyAccessUnlessGranted(
            PermissionType::USER_INVITE->value
        );

        $data = json_decode($request->getContent(), true);
        $dto = new AddUserToWorkspaceDTO();
        $dto->email = $data['email'] ?? null;
        $dto->role = $data['role'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->workspaceManager->addUser($workspace, $dto, $this->getUser());

        return $this->json(null, 204);
    }

    #[Route('/{id}', name: 'app_workspace_user_update', methods: ['PATCH'])]
    public function updateUser(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse {
        $this->denyAccessUnlessGranted(
            PermissionType::USER_EDIT->value
        );

        $workspaceUser = $this->workspaceUserManager->get($id);

        $data = json_decode($request->getContent(), true);
        $dto = new UpdateUserInWorkspaceDTO();
        $dto->role = $data['role'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->workspaceUserManager->updateUser(
            $workspaceUser,
            $dto,
            $this->getUser()
        );

        return $this->json(
            WorkspaceUserMapper::userToDTO($workspaceUser)
        );

    }

     #[Route('/{id}', name: 'app_workspace_user_delete', methods: ['DELETE'])]
     public function delete(
        int $id
     ) : JsonResponse {
        $this->denyAccessUnlessGranted(
            PermissionType::USER_DELETE->value
        );
        $workspaceUser = $this->workspaceUserManager->get($id);

        $this->workspaceUserManager->delete($workspaceUser, $this->getUser());

        return $this->json(null, 204);
        
     }
}