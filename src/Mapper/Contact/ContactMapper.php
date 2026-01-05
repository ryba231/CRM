<?php
namespace App\Mapper\Contact;

use App\Entity\Contact\Contact;
use ResponseContactDTO;

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
            $contact->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }
}