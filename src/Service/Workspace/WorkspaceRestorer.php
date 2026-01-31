<?php
namespace App\Service\Workspace;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Event\WorkspaceRestoredEvent;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class WorkspaceRestorer {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ) {}


    public function restore(
        Workspace $workspace,
        User $actor
    ) : void {
        if(!$workspace->isDeleted()) throw new DomainException('Workspace is not deleted');

        $workspace->restore();
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new WorkspaceRestoredEvent($workspace, $actor), 'workspace.restored');
    }
}

