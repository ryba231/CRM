<?php
namespace App\DTO\Workspace;

final class ResponseListUserDTO
{
    public function __construct(
        public int $id,
        public int $user_id,
        public string $full_name,
        public string $email,
        public string $role,
        public string $created_at
    ){}
}