<?php
namespace App\Service\Contact;

use App\DTO\Contact\CreateContactDTO;
use App\DTO\Contact\UpdateContactDTO;
use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Event\ContactCreatedEvent;
use App\Event\ContactDeletedEvent;
use App\Event\ContactUpdatedEvent;
use App\Repository\Contact\ContactRepository;
use App\Service\AuditLog\ChangeSetComparator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContactManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository,
        private EventDispatcherInterface $dispatcher,
        private ChangeSetComparator $changeSetComparator
    )
    {}

    public function createContact(
        CreateContactDTO $dto,
        User $owner,
        Workspace $workspace
    ) : Contact {

        $contact = new Contact();
        $contact->setEmail($dto->email);
        $contact->setFirstName($dto->firstName);
        $contact->setLastName($dto->lastName);
        $contact->setPhone($dto->phone);
        $contact->setOwner($owner);
        $contact->setStatus($dto->status ?? 'new');
        $contact->setType($dto->type);
        $contact->setWorkspace($workspace);

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new ContactCreatedEvent($contact, $owner), 'contact.created');

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
        return $this->contactRepository->findByOwnerPaginated($page, $limit, $owner);
    }

    public function getAllContactByWorkspace(
        Workspace $workspace,
        int $page,
        int $limit,
        ?string $search
    ) : array {
        return $this->contactRepository->findByWorkspacePaginated($workspace, $page, $limit, $search);
    }

    public function update(
        Contact $contact,
        UpdateContactDTO $dto,
        User $actor
    ) : Contact {
        $before = $this->auditLogData($contact);

        if($dto->email) $contact->setEmail($dto->email);
        if($dto->firstName) $contact->setFirstName($dto->firstName);
        if($dto->lastName) $contact->setLastName($dto->lastName);
        if($dto->phone) $contact->setPhone($dto->phone);
        if($dto->status) $contact->setStatus($dto->status);
        if($dto->type) $contact->setType($dto->type);

        $changes = $this->changeSetComparator->diff($before, $this->auditLogData($contact));

        $this->entityManager->flush();

        if($changes !== []) {
            $this->dispatcher->dispatch(new ContactUpdatedEvent($contact, $actor, $changes), 'contact.updated');
        }

        return $contact;
    }

    public function delete(
        Contact $contact,
        User $actor
    ) : void {
        $this->entityManager->remove($contact);

        $this->dispatcher->dispatch(new ContactDeletedEvent($contact, $actor), 'contact.deleted');

        $this->entityManager->flush();
    }

    private function auditLogData(
        Contact $contact
    ): array {
        return [
            'email' => $contact->getEmail(),
            'first_name' => $contact->getFirstName(),
            'last_name' => $contact->getLastName(),
            'phone' => $contact->getPhone(),
            'status' => $contact->getStatus(),
            'type' => $contact->getType()
        ];
    }
}
