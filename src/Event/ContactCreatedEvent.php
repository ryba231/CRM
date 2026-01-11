<?php
namespace App\Event;

use App\Entity\Contact\Contact;
use App\Entity\User\User;

final readonly class ContactCreatedEvent
{
    public function __construct(
        public readonly Contact $contact,
        public readonly User $actor
    ){}
}