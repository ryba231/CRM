<?php

namespace App\Security\Voter;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Security\Enum\PermissionType;
use App\Security\RolePermissionMap;
use App\Service\Workspace\WorkspaceContextResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class WorkspaceVoter extends Voter
{
    public function __construct(
        private RolePermissionMap $rolePermissionMap,
        private WorkspaceContextResolver $workspaceContextResolver,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!$subject instanceof Workspace) return false;

        return in_array($attribute, [
            PermissionType::CONTACT_VIEW->value,
            PermissionType::CONTACT_CREATE->value,
            PermissionType::WORKSPACE_MANAGE->value,
            PermissionType::USER_EDIT->value,
            PermissionType::USER_INVITE->value,
            PermissionType::USER_VIEW->value,
        ], true);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if(in_array('ROLE_ADMIN', $user->getRoles(), true))
        {
            return true;
        }

        /** @var Workspace $workspace */
        $workspace = $subject;
        $permission = PermissionType::from($attribute);

        $workspaceUser = $this->workspaceContextResolver->resolve($user, $workspace);

        if (!$workspaceUser) {
            return false;
        }

        return $this->rolePermissionMap->roleHasPermission(
            $workspaceUser->getRole(),
            $permission
        );

    }
}
