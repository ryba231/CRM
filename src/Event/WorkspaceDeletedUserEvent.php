<?php
namespace App\Event;

use App\Entity\User\User;
use App\Entity\Workspace\WorkspaceUser;

final readonly class WorkspaceDeletedUserEvent
{
    public function __construct(
        public readonly WorkspaceUser $workspaceUser,
        public readonly User $actor,
        public readonly array $changes
    ){}
}