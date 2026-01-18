<?php
namespace App\Controller;

use App\Mapper\Workspace\WorkspaceMapper;
use App\Service\Workspace\WorkspaceManager;
use App\Service\Workspace\WorkspaceRestorer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/admin/workspace')]
final class WorkspaceAdminController extends BaseApiController
{
    public function __construct(
        private WorkspaceManager $workspaceManager,
        private WorkspaceRestorer $restorer
    ){}

    #[Route('/{id}/restore', name: 'app_admin_workspace_restore', methods: ['POST'])]
    public function restore(
        int $id
    ) : JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $workspace = $this->workspaceManager->get($id);
        $this->restorer->restore(
            $workspace,
            $this->getUser()
        );

        return $this->json(
            WorkspaceMapper::toDTO($workspace),
            201
        );
    }
}