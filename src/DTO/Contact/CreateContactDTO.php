<?php
namespace App\DTO\Contact;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateContactDTO
{
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Invalid email')]
    #[Assert\Length(max: 255)]
    public ?string $email;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    public ?string $phone;

    #[Assert\Length(max: 255)]
    public ?string $firstName;
    
    #[Assert\Length(max: 255)]
    public ?string $lastName;
    
    #[Assert\Choice(choices: ['new', 'prospect', 'customer', 'inactive'])]
    public ?string $status = 'new';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['lead', 'referral', 'marketing'], message: 'Invalid contact type')]
    public ?string $type;
}