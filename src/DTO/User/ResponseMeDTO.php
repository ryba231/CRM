<?php
namespace App\DTO\User;

use App\Entity\User\User;

final class ResponseMeDTO
{
    public static function fromUser(User $user) : array {
        return [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "first_name" => $user->getFirstName(),
            "last_name" => $user->getLastName(),
            "created_at" => $user->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}