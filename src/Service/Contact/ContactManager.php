<?php
namespace App\Service\Contact;

use App\DTO\Contact\CreateContactDTO;
use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Repository\Contact\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ContactManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository
    )
    {}

    public function createContact(
        CreateContactDTO $dto,
        User $owner
    ) : Contact {

        $contact = new Contact();
        $contact->setEmail($dto->email);
        $contact->setFirstName($dto->firstName);
        $contact->setLastName($dto->lastName);
        $contact->setPhone($dto->phone);
        $contact->setOwner($owner);
        $contact->setStatus($dto->status ?? 'new');
        
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }
}