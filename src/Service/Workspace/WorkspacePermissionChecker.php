<?php

namespace App\Service\Workspace;

use App\Entity\Workspace\WorkspaceUser;
use App\Security\Enum\PermissionType;
use App\Security\RolePermissionMap;

final readonly class WorkspacePermissionChecker
{
    public function __construct(
         private RolePermissionMap $rolePermissionMap
    )
    {}

    public function hasPermission(
        WorkspaceUser $membership,
        PermissionType $permission
    ) : bool {

        return $this->rolePermissionMap->roleHasPermission(
            role: $membership->getRole(),
            permission: $permission
        );
    }
}