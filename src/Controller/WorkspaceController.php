<?php

namespace App\Controller;

use App\DTO\Workspace\AddUserToWorkspaceDTO;
use App\DTO\Workspace\CreateWorkspaceDTO;
use App\DTO\Workspace\UpdateWorkspaceDTO;
use App\Entity\Workspace\WorkspaceUser;
use App\Mapper\Workspace\WorkspaceMapper;
use App\Mapper\Workspace\WorkspaceUserMapper;
use App\Service\Workspace\WorkspaceManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/workspace')]
final class WorkspaceController extends BaseApiController
{
    public function __construct(private WorkspaceManager $workspaceManager)
    {}

    #[Route(name: 'app_workspace_list', methods: ['GET'])]
    public function list(
        Request $request
    ) : JsonResponse {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, max(1, $request->query->getInt('limit', 10)));
        $owner = $this->getUser();

        $result = $this->workspaceManager->getAllWorkspace($page, $limit, $owner);

        $workspaceDto = array_map(
            fn(WorkspaceUser $workspaceUser) => WorkspaceUserMapper::toDTO($workspaceUser),
            $result['items']
        );

        return $this->json(
            [
                'data' => $workspaceDto,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $result['total'],
                    'pages' => (int) ceil($result['total'] / $limit),
                ]
            ],
            200
        );
    }

    #[Route(name: 'app_workspace_create', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateWorkspaceDTO();
        $dto->name = $data['name'] ?? null;

        $owner = $this->getUser();

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $workspace = $this->workspaceManager->createWorkspace($dto, $owner);

        return $this->json(
            WorkspaceMapper::toDTO($workspace),
            201
        );
    }

    #[Route('/{id}', name: 'app_workspace_show', methods: ['GET'])]
    public function show(
        int $id
    ) : JsonResponse {
        $workspace = $this->workspaceManager->get($id);

        return $this->json(
            WorkspaceMapper::toDTO($workspace)
        );
    }

    #[Route('/{id}', name: 'app_workspace_update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse {
        $workspace = $this->workspaceManager->get($id);

        $data = json_decode($request->getContent(), true);
        $dto = new UpdateWorkspaceDTO();
        $dto->name = $data['name'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->workspaceManager->update(
            $workspace,
            $dto
        );

        return $this->json(
            WorkspaceMapper::toDTO($workspace),
            200
        );
    }
    #[Route('/{id}', name: 'app_workspace_delete', methods: ['DELETE'])]
    public function delete(
        int $id
    ) : JsonResponse {
        $workspace = $this->workspaceManager->get($id);

        $this->workspaceManager->delete($workspace);

        return $this->json(null, 204);
    }

    #[Route('/{id}/user', name: 'app_workspace_add_user', methods: ['POST'])]
    public function addUser(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ) : JsonResponse{
        $workspace = $this->workspaceManager->get($id);

        $data = json_decode($request->getContent(), true);
        $dto = new AddUserToWorkspaceDTO();
        $dto->userId = $data['userId'] ?? null;
        $dto->role = $data['role'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $this->workspaceManager->addUser($workspace, $dto);

        return $this->json(null, 204);
    }
}
