<?php

namespace App\Service\AuditLog;

use App\Entity\AuditLog\AuditLog;
use Doctrine\ORM\EntityManagerInterface;

final readonly class AuditLogManager 
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function log(
        string $entity,
        ?int $entityId,
        string $action,
        ?array $changes,
        ?int $userId
    ): void {
        $audit = new AuditLog(
            entity: $entity,
            entityId: $entityId,
            action: $action,
            changes: $changes,
            userId: $userId
        );

        $this->em->persist($audit);
        $this->em->flush();
    }
}