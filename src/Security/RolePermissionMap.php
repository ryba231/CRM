<?php
namespace App\Security;

use App\Security\Enum\PermissionType;
use App\Security\Enum\WorkspaceRoleType;

final class RolePermissionMap
{
    public function permissionsForRole(
        WorkspaceRoleType $role
    ) : array {
        return match ($role) {
            WorkspaceRoleType::OWNER => PermissionType::cases(),

            WorkspaceRoleType::ADMIN => [
                PermissionType::CONTACT_VIEW,
                PermissionType::CONTACT_CREATE,
                PermissionType::CONTACT_EDIT,
                PermissionType::CONTACT_DELETE,
                PermissionType::USER_VIEW,
                PermissionType::USER_EDIT,
                PermissionType::USER_INVITE,
                PermissionType::WORKSPACE_CREATE,
                PermissionType::WORKSPACE_DELETE,
                PermissionType::WORKSPACE_EDIT,
                PermissionType::WORKSPACE_VIEW
            ],

            WorkspaceRoleType::MEMBER => [
                PermissionType::CONTACT_VIEW,
                PermissionType::CONTACT_CREATE,
                PermissionType::CONTACT_EDIT,
                PermissionType::WORKSPACE_VIEW
            ]
        };
    }

    public function roleHasPermission(
        WorkspaceRoleType $role,
        PermissionType $permission
    ): bool
    {
        return in_array(
            $permission,
            $this->permissionsForRole($role),
            true
        );
    }
}
