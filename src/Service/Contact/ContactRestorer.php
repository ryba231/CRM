<?php
namespace App\Service\Contact;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Event\ContactRestoredEvent;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContactRestorer
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ){}

    public function restore(
        Contact $contact,
        User $actor
    ) : void {
        if(!$contact->isDeleted()) throw new DomainException('Contact is not deleted');

        $contact->restore();
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new ContactRestoredEvent($contact, $actor), 'contact.restored');
    }
    
}