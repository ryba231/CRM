<?php
namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;
final class RegisterUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Email(message: 'Invalid email')]
    public ?string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public ?string $password;

    #[Assert\Length(max: 255)]
    public ?string $firstName;

    #[Assert\Length(max: 255)]
    public ?string $lastName;
}
