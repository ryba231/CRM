<?php

namespace App\EventListener;

use App\Event\ContactCreatedEvent;
use App\Event\ContactDeletedEvent;
use App\Event\ContactUpdatedEvent;
use App\Event\WorkspaceCreatedEvent;
use App\Event\WorkspaceDeletedEvent;
use App\Event\WorkspaceUpdatedEvent;
use App\Service\AuditLog\AuditLogManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class AuditLogListener
{
    public function __construct(
        private AuditLogManager $auditLogManager
    )
    {}

    #[AsEventListener(event: 'kernel.event_listener')]
    public function onKernelEventListener($event): void
    {
        // ...
    }

    public function onContactCreated(
        ContactCreatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'create',
            changes: null,
            userId: $event->user->getId()
        );
    }

    public function onContactUpdated(
        ContactUpdatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'update',
            changes: null,
            userId: $event->user->getId()
        );
    }

    public function onContactDeleted(
        ContactDeletedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'delete',
            changes: null,
            userId: $event->user->getId()
        );
    }

    public function onWorkspaceCreated(
        WorkspaceCreatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'create',
            changes: null,
            userId: $event->user->getId()
        );
    }

    public function onWorkspaceUpdated(
        WorkspaceUpdatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'update',
            changes: null,
            userId: $event->user->getId()
        );
    }

    public function onWorkspaceDeleted(
        WorkspaceDeletedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'delete',
            changes: null,
            userId: $event->user->getId()
        );
    }


}
