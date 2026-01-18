<?php
namespace App\Mapper\Workspace;

use App\DTO\Workspace\ResponseWorkspaceDTO;
use App\Entity\Workspace\Workspace;

class WorkspaceMapper
{
    public static function toDTO(
        Workspace $workspace
    ) : ResponseWorkspaceDTO {
        return new ResponseWorkspaceDTO(
            $workspace->getId(),
            $workspace->getName(),
            $workspace->getCreatedAt()->format('Y-m-d H:i:s'),
            $workspace->getUpdatedAt()->format('Y-m-d H:i:s'),
            $workspace->getDeletedAt()?->format('Y-m-d H:i:s')
        );
    }
}