<?php
namespace App\DTO\Workspace;

use Symfony\Component\Validator\Constraints as Assert;

final class AddUserToWorkspaceDTO {

    #[Assert\NotBlank]
    #[Assert\Email(message: 'Invalid email')]
    public ?string $email = null;

    #[Assert\Choice(choices:['admin','member'])]
    public ?string $role = null;
}
