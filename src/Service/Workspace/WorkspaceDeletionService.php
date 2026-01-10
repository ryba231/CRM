<?php
namespace App\Service\Workspace;

use App\Entity\Workspace\Workspace;
use Doctrine\ORM\EntityManagerInterface;

final readonly class WorkspaceDeletionService {
    public function __construct(
        private EntityManagerInterface $em
    ) {}


    public function delete(
        Workspace $workspace
    ) : void {
        $workspace->softDelete();
        $this->em->flush();
    }
}

