<?php
namespace App\DTO\Workspace;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserInWorkspaceDTO
{
    #[Assert\Choice(choices:['admin','member'])]
    public ?string $role;
}