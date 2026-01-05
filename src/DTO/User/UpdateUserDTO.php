<?php
namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;
class UpdateUserDTO
{
    #[Assert\Email(message: 'Invalid email')]
    #[Assert\Length(max: 255)]
    public ?string $email = null;

    #[Assert\Length(max: 255)]
    public ?string $firstName = null;

    #[Assert\Length(max: 255)]
    public ?string $lastName = null;
    
    public ?array $roles = null;
}