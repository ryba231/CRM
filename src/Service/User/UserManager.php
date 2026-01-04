<?php
namespace App\Service\User;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\ResponseMeDTO;
use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserManager
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private Security $security)
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

    public function get(
        int $id
    ) : User  { 
        return $this->userRepository->findOrFail($id);
    }

    public function getAllUsers(
        int $page, 
        int $limit
    ): array {
        return $this->userRepository->findPaginated($page, $limit);
    }

    public function getCurrentUser(): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('User not authenticated');
        }

        return ResponseMeDTO::fromUser($user);
        
    }

    public function update(
        User $user, 
        ?string $email,
        ?string $firstName,
        ?string $lastName,
        ?array $roles
    ) : User {
        
        if($email) $user->setEmail($email);
        if($firstName) $user->setFirstName($firstName);
        if($lastName) $user->setLastName($lastName);
        if($roles) $user->setRoles($roles);

        $this->entityManager->flush();

        return $user;
    }

    public function delete(
       User $user
    ): void {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
