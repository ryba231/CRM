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

    #[AsEventListener(event: 'contact.created')]
    public function onContactCreated(
        ContactCreatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'create',
            changes: null,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'contact.updated')]
    public function onContactUpdated(
        ContactUpdatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'update',
            changes: $event->changes,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'contact.deleted')]
    public function onContactDeleted(
        ContactDeletedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Contact',
            entityId: $event->contact->getId(),
            action: 'delete',
            changes: null,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'workspace.created')]
    public function onWorkspaceCreated(
        WorkspaceCreatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'create',
            changes: null,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'workspace.updated')]
    public function onWorkspaceUpdated(
        WorkspaceUpdatedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'update',
            changes: $event->changes,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'workspace.deleted')]
    public function onWorkspaceDeleted(
        WorkspaceDeletedEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: 'delete',
            changes: null,
            userId: $event->actor->getId()
        );
    }


}
