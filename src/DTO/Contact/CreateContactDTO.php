<?php
namespace App\DTO\Contact;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $phone;

    public ?string $firstName;
    public ?string $lastName;
    public ?string $status = 'new';
    public ?string $type = null;
}