<?php
namespace App\Service\User;

use App\Entity\User\User;
use App\Event\UserAnonymizedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UserAnonymizer
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ){}

    public function anonymize(
        User $target,
        User $actor
    ) : void {
        $target->anonymize();
        $this->entityManager->flush();

        $this->dispatcher->dispatch(
            new UserAnonymizedEvent($target, $actor), 'user.anonymized'
        );
    }
}