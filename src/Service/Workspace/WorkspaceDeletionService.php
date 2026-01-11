<?php
namespace App\Service\Workspace;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Event\WorkspaceDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class WorkspaceDeletionService {
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $dispatcher
    ) {}


    public function delete(
        Workspace $workspace,
        User $actor
    ) : void {
        $workspace->softDelete();
        $this->em->flush();

        $this->dispatcher->dispatch(new WorkspaceDeletedEvent($workspace, $actor), 'workspace.deleted');
    }
}

