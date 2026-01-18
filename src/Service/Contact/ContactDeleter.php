<?php
namespace App\Service\Contact;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Event\ContactDeletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContactDeleter
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher
    ){}
    
    public function delete(
        Contact $contact,
        User $actor
    ):void {
       if ($contact->isDeleted()) return;
       
       $contact->softDelete();
       $this->entityManager->flush();

       $this->dispatcher->dispatch(new ContactDeletedEvent($contact, $actor), 'contact.deleted');
    
    }
}