<?php

namespace App\Controller;

use App\DTO\Contact\CreateContactDTO;
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
    public function list()
    {
    
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
    public function show()
    {
        
    }

    #[Route('/{id}', name: 'app_contact_update', methods: ['PATCH'])]
    public function update()
    {

    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['DELETE'])]
    public function delete()
    {
    
    }
}
