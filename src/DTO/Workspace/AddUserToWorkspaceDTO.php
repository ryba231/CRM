<?php
namespace App\DTO\Workspace;

use Symfony\Component\Validator\Constraints as Assert;

final class AddUserToWorkspaceDTO {

    #[Assert\NotBlank]
    public ?int $userId = null;

    #[Assert\Choice('admin','member')]
    public ?string $role = null;
}