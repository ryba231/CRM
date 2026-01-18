<?php
namespace App\Event;

use App\Entity\User\User;

final readonly class UserRestoredEvent
{
    public function __construct(
        public readonly User $user,
        public readonly User $actor,
    ){}
}