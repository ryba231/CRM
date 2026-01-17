<?php
namespace App\Event;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;

final readonly class WorkspaceAddedUserEvent
{
    public function __construct(
        public readonly Workspace $workspace,
        public readonly User $actor,
        public readonly array $changes
    ){}
}