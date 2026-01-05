<?php
namespace App\Service\Contact;

use App\DTO\Contact\CreateContactDTO;
use App\DTO\Contact\UpdateContactDTO;
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
        $contact->setType($dto->type);
        
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }

    public function get(
        int $id
    ) : Contact {
        return $this->contactRepository->findOrFail($id);    
    }

    public function getAllContact(
        int $page,
        int $limit,
        User $owner
    ) : array {
        return $this->contactRepository->findPaginatedByOwner($page, $limit, $owner);
    }

    public function update(
        Contact $contact,
        UpdateContactDTO $dto
    ) : Contact {
        
        if($dto->email) $contact->setEmail($dto->email);
        if($dto->firstName) $contact->setFirstName($dto->firstName);
        if($dto->lastName) $contact->setLastName($dto->lastName);
        if($dto->phone) $contact->setPhone($dto->phone);
        if($dto->status) $contact->setStatus($dto->status);
        if($dto->type) $contact->setType($dto->type);

        $this->entityManager->flush();
        return $contact;
    }

    public function delete(
        Contact $contact
    ) : void {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }
}