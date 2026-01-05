<?php

namespace App\Controller;

use App\DTO\Contact\CreateContactDTO;
use App\DTO\Contact\UpdateContactDTO;
use App\Entity\Contact\Contact;
use App\Form\Contact\ContactType;
use App\Mapper\Contact\ContactMapper;
use App\Repository\Contact\ContactRepository;
use App\Service\Contact\ContactManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/contact')]
final class ContactController extends AbstractController
{

    public function __construct(private ContactManager $contactManager){}
    #[Route(name: 'app_contact_list', methods: ['GET'])]
    public function list(
        Request $request
    ): JsonResponse {
            $page = max(1, $request->query->getInt('page', 1));
            $limit = min(50, max(1, $request->query->getInt('limit', 10)));
            $owner = $this->getUser();

            try {
                $result = $this->contactManager->getAllContact($page, $limit, $owner);
                $contactsDto = array_map(
                    fn(Contact $contact) => ContactMapper::toDTO($contact),
                    $result['items']
                );
            } catch(\DomainException $e) {
                return $this->json(['error' => $e->getMessage()], 404);
            }

            return $this->json(
                [
                    'data' => $contactsDto,
                    'meta' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $result['total'],
                        'pages' => (int) ceil($result['total'] / $limit),
                    ]
                ],
                200);
    }

    #[Route(name: 'app_contact_new', methods: ['POST'])]
    public function new(
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateContactDTO();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->status = $data['status'] ?? null;
        $dto->type = $data['type'] ?? null;

        $owner = $this->getUser();

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], 400);
        }

        try {
            $contact = $this->contactManager->createContact($dto, $owner);
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], 409);
        }

        return $this->json(
            ContactMapper::toDTO($contact),
            201
        );
    }

    #[Route('/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(
        int $id
    ): JsonResponse {
        return $this->json(
            ContactMapper::toDTO($this->contactManager->get($id))
        );
    }

    #[Route('/{id}', name: 'app_contact_update', methods: ['PATCH'])]
    public function update(
        int $id,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new UpdateContactDTO();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? null;
        $dto->lastName = $data['last_name'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->status = $data['status'] ?? null;
        $dto->type = $data['type'] ?? null;

        $contact = $this->contactManager->get($id);

        $errors = $validator->validate($dto);

        if(count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], 400);
        }

        $this->contactManager->update(
            $contact,
            $dto
        );

        return $this->json(
            ContactMapper::toDTO($contact),
            200
        );
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['DELETE'])]
    public function delete(
        int $id
    ): JsonResponse {
        $this->contactManager->delete(
            $this->contactManager->get($id)
        );

        return $this->json(null, 204);
    }
}
