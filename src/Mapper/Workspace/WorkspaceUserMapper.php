<?php
namespace App\Mapper\Workspace;

use App\DTO\Workspace\ResponseListWorkspaceDTO;
use App\Entity\Workspace\WorkspaceUser;

class WorkspaceUserMapper
{
    public static function toDTO(
        WorkspaceUser $workspaceUser
    ) : ResponseListWorkspaceDTO {
        return new ResponseListWorkspaceDTO(
            $workspaceUser->getId(),
            $workspaceUser->getWorkspace()->getName(),
            $workspaceUser->getRole()->value,
            $workspaceUser->getWorkspace()->getCreatedAt()->format('Y-m-d H:i:s'),
            $workspaceUser->getWorkspace()->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }
}
