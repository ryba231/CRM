<?php
namespace App\DTO\User;

final class ResponseUserDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public string $firstName,
        public string $lastName,
        public array $roles,
        public string $created_at,
        public ?string $deleted_at
    ) {}
}