<?php
namespace App\Mapper\Workspace;

use App\DTO\Workspace\ResponseListUserDTO;
use App\DTO\Workspace\ResponseListWorkspaceDTO;
use App\Entity\Workspace\WorkspaceUser;

class WorkspaceUserMapper
{
    public static function workspaceToDTO(
        WorkspaceUser $workspaceUser
    ) : ResponseListWorkspaceDTO {
        return new ResponseListWorkspaceDTO(
            $workspaceUser->getWorkspace()->getId(),
            $workspaceUser->getWorkspace()->getName(),
            $workspaceUser->getRole()->value,
            $workspaceUser->getWorkspace()->getCreatedAt()->format('Y-m-d H:i:s'),
            $workspaceUser->getWorkspace()->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }

    public static function userToDTO(
        WorkspaceUser $workspaceUser
    ) : ResponseListUserDTO {
        return new ResponseListUserDTO(
            $workspaceUser->getId(),
            $workspaceUser->getUser()->getId(),
            $workspaceUser->getUser()->getFullName(),
            $workspaceUser->getUser()->getEmail(),
            $workspaceUser->getRole()->value,
            $workspaceUser->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }
}
