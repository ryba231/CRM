<?php
namespace App\Security;

use App\Security\Enum\PermissionType;
use App\Security\Enum\WorkspaceRoleType;

class RolePermissionMap
{
    public static function getPermissions(
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
            ],

            WorkspaceRoleType::MEMBER => [
                PermissionType::CONTACT_VIEW,
                PermissionType::CONTACT_CREATE,
                PermissionType::CONTACT_EDIT,
            ]
        };
    }
}