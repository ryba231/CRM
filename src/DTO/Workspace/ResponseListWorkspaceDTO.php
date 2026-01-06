<?php
namespace App\DTO\Workspace;

final class ResponseListWorkspaceDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $role
,       public string $created_at,
        public string $updated_at
    )
    {}
}