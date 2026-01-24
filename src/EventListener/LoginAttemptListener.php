<?php

namespace App\EventListener;

use App\Service\LoginAttempt\LoginAttemptManager;
use App\Service\User\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

final class LoginAttemptListener
{
    public function __construct(
        private LoginAttemptManager $loginAttemptManager,
        private RequestStack $requestStack,
        private UserManager $userManager
    ) {}

    #[AsEventListener]
    public function onLexikSuccess(AuthenticationSuccessEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $user = $event->getUser();

        $this->loginAttemptManager->log(
            email: $user->getEmail(),
            success: true,
            ipAddress: $request?->getClientIp() ?? 'unknown',
            userAgent: $request?->headers->get('User-Agent'),
            user: $user
        );
    }

    #[AsEventListener]
    public function onLexikFailure(AuthenticationFailureEvent $event) : void
    {
        $request = $event->getRequest();
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? 'unknown';

        $user = $email !== 'unknown'
            ? $this->userManager->getByEmail($email)
            : null;

        $this->loginAttemptManager->log(
            email: (string) $email,
            success: false,
            ipAddress: $request?->getClientIp() ?? 'unknown',
            userAgent: $request?->headers->get('User-Agent'),
            user: $user
        );    
    }
}
