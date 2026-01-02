<?php
namespace App\DTO\User;
use Symfony\Component\Validator\Constraints as Assert;
final class RegisterUserDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;

    public ?string $firstName;
    public ?string $lastName;
}
