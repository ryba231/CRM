<?php

namespace App\Security\Voter;

use App\Entity\User\User;
use App\Security\Enum\PermissionType;
use App\Service\Workspace\WorkspaceContextInterface;
use App\Service\Workspace\WorkspaceContextResolver;
use App\Service\Workspace\WorkspacePermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public function __construct(
        private WorkspaceContextInterface $workspaceContext,
        private WorkspaceContextResolver $contextResolver,
        private WorkspacePermissionChecker $permissionChecker
    ){}

    protected function supports(
        string $attribute, 
        mixed $subject
    ): bool {
        return PermissionType::tryFrom($attribute) !== null;
    }

    protected function voteOnAttribute(
        string $attribute, 
        mixed $subject, 
        TokenInterface $token
    ): bool {
        $user = $token->getUser();
        
        if(!$user instanceof User) return false;

        $workspace = $this->workspaceContext->getCurrentWorkspace();

        $membership = $this->contextResolver->resolve($user, $workspace);

        if(!$membership) return false;

        return $this->permissionChecker->hasPermission(
            membership: $membership,
            permission: PermissionType::from($attribute)
        );
    }
}
