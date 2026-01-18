<?php
namespace App\Service\User;

use App\Entity\User\User;
use App\Event\UserRestoredEvent;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UserRestorer 
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ){}

    public function restore(
        User $user,
        User $actor
    ) : void {
        if(!$user->isDeleted()) throw new DomainException('Contact is not deleted');

        $user->restore();
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new UserRestoredEvent($user, $actor), 'user.restored');
    }
}