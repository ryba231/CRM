<?php

namespace App\DTO\Workspace;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateWorkspaceDTO 
{
    #[Assert\NotBlank]
    public ?string $name;
}