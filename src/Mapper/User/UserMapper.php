<?php
namespace App\Mapper\User;

use App\DTO\User\ResponseUserDTO;
use App\Entity\User\User;

class UserMapper
{
    public static function toDTO(User $user): ResponseUserDTO
    {
        return new ResponseUserDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getRoles(),
            $user->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }
}