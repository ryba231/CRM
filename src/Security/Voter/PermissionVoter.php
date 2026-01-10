<?php

namespace App\Security\Voter;

use App\Service\Workspace\WorkspaceContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public function __construct(
    )
    {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // TODO: Implement supports() method.
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // TODO: Implement voteOnAttribute() method.
    }
}
