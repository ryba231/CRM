<?php
namespace App\Service\User;

use App\DTO\User\RegisterUserDTO;
use App\DTO\User\ResponseMeDTO;
use App\DTO\User\UpdateUserDTO;
use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserManager
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

        $workspace = new Workspace();
        $workspace->setName("Workspace: " . $user->getEmail());

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($user);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('owner');

        $this->entityManager->persist($workspace);
        $this->entityManager->persist($user);
        $this->entityManager->persist($workspaceUser);
        $this->entityManager->flush();

        return $user;
    }

    public function get(
        int $id
    ) : User  {
        return $this->userRepository->findOrFail($id);
    }

    public function getByEmail(
        string $email
    ): ?User {
        return $this->userRepository->findByEmail($email);
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
        UpdateUserDTO $dto
    ) : User {

        if($dto->email) $user->setEmail($dto->email);
        if($dto->firstName) $user->setFirstName($dto->firstName);
        if($dto->lastName) $user->setLastName($dto->lastName);
        if($dto->roles) $user->setRoles($dto->roles);

        $this->entityManager->flush();

        return $user;
    }
}
