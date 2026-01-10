<?php

namespace App\Service\Workspace;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use App\Repository\Workspace\WorkspaceUserRepository;


final readonly class WorkspaceContextResolver
{
    public function __construct(
        private WorkspaceUserRepository $workspaceUserRepository
    ) {}

    public function resolve(
        User $user,
        Workspace $workspace,
    ): ?WorkspaceUser {
        return $this->workspaceUserRepository->findByWorkspaceAndUser($workspace, $user);
    }
}
