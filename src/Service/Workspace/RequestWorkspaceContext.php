<?php
namespace App\Service\Workspace;

use App\Entity\Workspace\Workspace;
use App\Repository\Workspace\WorkspaceRepository;
use App\Service\Workspace\WorkspaceContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequestWorkspaceContext implements WorkspaceContextInterface
{
    private ?Workspace $workspace = null;

    public function __construct(
        private RequestStack $requestStack,
        private WorkspaceRepository $workspaceRepository
    ) {}

    public function getCurrentWorkspace(): Workspace
    {
        if ($this->workspace) return $this->workspace;

        $request = $this->requestStack->getCurrentRequest();
        $workspaceId = $request?->headers->get('X-Workspace-Id');

        if(!$workspaceId) throw new AccessDeniedHttpException('Workspace header missing');

        $workspace = $this->workspaceRepository->findOrFail($workspaceId);

        return $this->workspace = $workspace;
    }
}
