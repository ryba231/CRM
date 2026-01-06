<?php
namespace App\DTO\Workspace;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateWorkspaceDTO 
{
    #[Assert\NotBlank]
    public ?string $name;
}