<?php

namespace App\Service\User;

use App\Entity\User\User;
use App\Event\UserDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UserDeleter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ){}

    public function delete(
        User $user,
        User $actor
    ): void {
        if($user->isDeleted()) return;

        $user->softDelete();
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new UserDeletedEvent($user, $actor), 'user.deleted');
    }
}