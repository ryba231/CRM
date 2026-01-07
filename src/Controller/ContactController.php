<?php

namespace App\Controller;

use App\DTO\Contact\CreateContactDTO;
use App\DTO\Contact\UpdateContactDTO;
use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Mapper\Contact\ContactMapper;
use App\Security\Enum\PermissionType;
use App\Service\Contact\ContactManager;
use App\Service\Workspace\WorkspaceManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/contact')]
final class ContactController extends BaseApiController
{

    public function __construct(
        private ContactManager $contactManager,
        private WorkspaceManager $workspaceManager
        ){}
    #[Route(name: 'app_contact_list', methods: ['GET'])]
    public function list(
        Request $request
    ): JsonResponse {
        $workspaceId = (int) $request->headers->get('X-Workspace-Id');

        if(!$workspaceId) throw new \DomainException('Workspace header missing');

        $workspace = $this->workspaceManager->getCurrentWorkspace($workspaceId, $this->getUser());

        $this->denyAccessUnlessGranted(
            PermissionType::CONTACT_VIEW->value,
            $workspace
        );

        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(50, max(1, $request->query->getInt('limit', 10)));

        $result = $this->contactManager->getAllContactByWorkspace($workspace, $page, $limit, null);
        $contactsDto = array_map(
            fn(Contact $contact) => ContactMapper::toDTO($contact),
            $result['items']
        );

        return $this->json(
            [
                'data' => $contactsDto,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $result['total'],
                    'pages' => (int) ceil($result['total'] / $limit),
                ]
            ]);
    }

    #[Route(name: 'app_contact_new', methods: ['POST'])]
    public function new(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $workspaceId = (int) $request->headers->get('X-Workspace-Id');

        if(!$workspaceId) throw new \DomainException('Workspace header missing');

        $workspace = $this->workspaceManager->getCurrentWorkspace($workspaceId, $this->getUser());

        $this->denyAccessUnlessGranted(
            PermissionType::CONTACT_CREATE->value,
            $workspace
        );

        $data = json_decode($request->getContent(), true);

        $dto = new CreateContactDTO();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->status = $data['status'] ?? null;
        $dto->type = $data['type'] ?? null;

        /** @var User $owner */
        $owner = $this->getUser();

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $contact = $this->contactManager->createContact($dto, $owner, $workspace);

        return $this->json(
            ContactMapper::toDTO($contact),
            201
        );
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(
        int $id
    ): JsonResponse {
        $contact = $this->contactManager->get($id);

        $this->denyAccessUnlessGranted(
            PermissionType::CONTACT_VIEW->value,
            $contact
        );

        return $this->json(
            ContactMapper::toDTO($contact)
        );
    }

    #[Route('/{id}', name: 'app_contact_update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $contact = $this->contactManager->get($id);

        $this->denyAccessUnlessGranted(
            PermissionType::CONTACT_EDIT->value,
            $contact
        );

        $data = json_decode($request->getContent(), true);

        $dto = new UpdateContactDTO();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->status = $data['status'] ?? null;
        $dto->type = $data['type'] ?? null;

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
           return $this->validationErrorResponse($errors);
        }

        $this->contactManager->update(
            $contact,
            $dto
        );

        return $this->json(
            ContactMapper::toDTO($contact)
        );
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $contact = $this->contactManager->get($id);

        $this->denyAccessUnlessGranted(
            PermissionType::CONTACT_DELETE->value,
            $contact
        );

        $this->contactManager->delete($contact);

        return $this->json(null, 204);
    }
}
