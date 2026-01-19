<?php
namespace App\Security\User;

use App\Entity\User\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if(!$user instanceof AppUser) return;

        if(!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException(
                'Account is inactive'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        
    }
}