<?php

namespace App\Service\LoginAttempt;

use App\Entity\LoginAttempt\LoginAttempt;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

final class LoginAttemptManager 
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}

    public function log(
        string $email,
        bool $success,
        string $ipAddress,
        ?string $userAgent,
        ?User $user = null
    ) : void {
        $attempt = new LoginAttempt(
            email: $email,
            success: $success,
            ipAddress: $ipAddress,
            userAgent: $userAgent,
            user: $user
        );

        $this->entityManager->persist($attempt);
        $this->entityManager->flush();
    }
}