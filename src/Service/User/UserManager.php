<?php
namespace App\Service\User;

use App\DTO\User\RegisterUserDTO;
use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserManager
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $passwordHasher)
    {}

    public function createUser(RegisterUserDTO $dto): User
    {
        $repo = $this->entityManager->getRepository(User::class);

        if ($repo->findOneBy(['email' => $dto->email])) {
            throw new \DomainException('User already exists');
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setFirstName($dto->firstName);
        $user->setLastName($dto->lastName);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $dto->password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
