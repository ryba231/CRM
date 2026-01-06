<?php
namespace App\DTO\Workspace;

final class ResponseWorkspaceDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $created_at,
        public string $updated_at
    )
    {}
}