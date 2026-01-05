<?php
namespace App\DTO\Contact;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateContactDTO
{
    #[Assert\Email(message: 'Invalid email')]
    #[Assert\Length(max: 255)]
    public ?string $email;

    #[Assert\Length(max: 20)]
    public ?string $phone;

    #[Assert\Length(max: 255)]
    public ?string $firstName;
    
    #[Assert\Length(max: 255)]
    public ?string $lastName;
    
    #[Assert\Choice(choices: ['new', 'prospect', 'customer', 'inactive'])]
    public ?string $status = 'new';

    #[Assert\Choice(choices: ['lead', 'referral', 'marketing'], message: 'Invalid contact type')]
    public ?string $type = null;
}