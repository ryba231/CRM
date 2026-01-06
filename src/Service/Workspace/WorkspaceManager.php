<?php
namespace App\Service\Workspace;

use App\DTO\Workspace\AddUserToWorkspaceDTO;
use App\DTO\Workspace\CreateWorkspaceDTO;
use App\DTO\Workspace\UpdateWorkspaceDTO;
use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use App\Repository\Workspace\WorkspaceRepository;
use App\Repository\Workspace\WorkspaceUserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class WorkspaceManager {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceUserRepository $workspaceUserRepository)
    {}

    public function createWorkspace(
        CreateWorkspaceDTO $dto,
        User $owner
    ) : Workspace {
        
        $workspace = new Workspace;
        $workspace->setName($dto->name);

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setUser($owner);
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setRole('owner');

        $this->entityManager->persist($workspace);
        $this->entityManager->persist($workspaceUser);
        $this->entityManager->flush();

        return $workspace;
    }

    public function get(
        int $id
    ) : Workspace {
        return $this->workspaceRepository->findOrFail($id);       
    }

    public function getAllWorkspace(
        int $page,
        int $limit,
        User $owner
    ) : array {
        return $this->workspaceUserRepository->findByUserPaginated($page, $limit, $owner);
    }

    public function update(
        Workspace $workspace,
        UpdateWorkspaceDTO $dto
    ) : Workspace {
        if($dto->name) $workspace->setName($dto->name);

        $this->entityManager->flush();

        return $workspace;
    }

    public function delete(
        Workspace $workspace
    ) : void {
        $this->entityManager->remove($workspace);
        $this->entityManager->flush();
    }

    public function addUser(
        Workspace $workspace,
        AddUserToWorkspaceDTO $dto
    ) : void {
        $user = $this->entityManager->getRepository(User::class)->find($dto->userId);

        if(!$user) throw new \DomainException('User not found');

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setUser($user);
        $workspaceUser->setRole($dto->role ?? '');

        $this->entityManager->persist($workspaceUser);
        $this->entityManager->flush();
    }

    public function getCurrentWorkspace(
        int $workspaceId,
        User $user
    ) : Workspace {
        $workspace = $this->workspaceRepository->findOrFail($workspaceId);

        $this->workspaceUserRepository->findByWorkspaceAndUser($workspace, $user);

        return $workspace;
    }
}