<?php
namespace App\DTO\User;

class UpdateUserDTO
{
    public ?string $email = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?array $roles = null;
}