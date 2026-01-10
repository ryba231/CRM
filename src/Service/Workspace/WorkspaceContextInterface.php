<?php
namespace App\Service\Workspace;

use App\Entity\Workspace\Workspace;

interface WorkspaceContextInterface
{
    public function getCurrentWorkspace(): Workspace;
}
