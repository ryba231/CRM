<?php

namespace App\EventListener;

use App\Event\ContactCreatedEvent;
use App\Event\ContactDeletedEvent;
use App\Event\ContactUpdatedEvent;
use App\Event\Enum\AuditAction;
use App\Event\WorkspaceCreatedEvent;
use App\Event\WorkspaceDeletedEvent;
use App\Event\WorkspaceUpdatedEvent;
use App\Event\WorkspaceAddedUserEvent;
use App\Event\WorkspaceDeletedUserEvent;
use App\Event\WorkspaceRoleChangedUserEvent;
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
            action: AuditAction::CREATE->value,
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
            action: AuditAction::UPDATE->value,
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
            action: AuditAction::DELETE->value,
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
            action: AuditAction::CREATE->value,
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
            action: AuditAction::UPDATE->value,
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
            action: AuditAction::DELETE->value,
            changes: null,
            userId: $event->actor->getId()
        );
    }

    #[AsEventListener(event: 'workspace.user.added')]
    public function onWorkspaceUserAdded(
        WorkspaceAddedUserEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspace->getId(),
            action: AuditAction::USER_ADDED->value,
            changes: $event->changes,
            userId: $event->actor->getId()
        );    
    }

    #[AsEventListener(event: 'workspace.user.deleted')]
    public function onWorkspaceUserDeleted(
        WorkspaceDeletedUserEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'Workspace',
            entityId: $event->workspaceUser->getId(),
            action: AuditAction::USER_DELETED->value,
            changes: $event->changes,
            userId: $event->actor->getId()
        );    
    }

    #[AsEventListener(event: 'workspace.user.role_changed')]
    public function onWorkspaceUserRoleChanged(
        WorkspaceRoleChangedUserEvent $event
    ) : void {
        $this->auditLogManager->log(
            entity: 'WorkspaceUser',
            entityId: $event->workspaceUser->getId(),
            action: AuditAction::USER_ROLE_CHANGED->value,
            changes: $event->changes,
            userId: $event->actor->getId()
        );    
    }


}
