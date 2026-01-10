<?php
namespace App\Event;

use App\Entity\Contact\Contact;
use App\Entity\User\User;

final readonly class ContactUpdatedEvent
{
    public function __construct(
        public readonly Contact $contact,
        public readonly User $user
    ){}
}