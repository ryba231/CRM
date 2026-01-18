<?php
namespace App\Mapper\Contact;

use App\DTO\Contact\ResponseContactDTO;
use App\Entity\Contact\Contact;

class ContactMapper
{
    public static function toDTO(
        Contact $contact
    ) : ResponseContactDTO {
        return new ResponseContactDTO(
            $contact->getId(),
            $contact->getFullName(),
            $contact->getEmail(),
            $contact->getPhone(),
            $contact->getStatus(),
            $contact->getType(),
            $contact->getCreatedAt()->format('Y-m-d H:i:s'),
            $contact->getDeletedAt()?->format('Y-m-d H:i:s')
        );
    }
}