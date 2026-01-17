<?php
namespace App\Service\Workspace;

use App\DTO\Workspace\AddUserToWorkspaceDTO;
use App\DTO\Workspace\CreateWorkspaceDTO;
use App\DTO\Workspace\UpdateUserInWorkspaceDTO;
use App\DTO\Workspace\UpdateWorkspaceDTO;
use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use App\Event\WorkspaceAddedUserEvent;
use App\Event\WorkspaceCreatedEvent;
use App\Event\WorkspaceUpdatedEvent;
use App\Repository\Workspace\WorkspaceRepository;
use App\Repository\Workspace\WorkspaceUserRepository;
use App\Service\AuditLog\ChangeSetComparator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WorkspaceManager {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceUserRepository $workspaceUserRepository,
        private EventDispatcherInterface $dispatcher,
        private ChangeSetComparator $changeSetComparator)
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

        $this->dispatcher->dispatch(new WorkspaceCreatedEvent($workspace, $owner), 'workspace.created');
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
        UpdateWorkspaceDTO $dto,
        User $actor
    ) : Workspace {
        $before = $this->auditLogData($workspace);

        if($dto->name) $workspace->setName($dto->name);

        $changes = $this->changeSetComparator->diff($before, $this->auditLogData($workspace));

        $this->entityManager->flush();

        if($changes !== []){
            $this->dispatcher->dispatch(new WorkspaceUpdatedEvent($workspace, $actor, $changes), 'workspace.updated');
        }

        return $workspace;
    }

    public function getAllUser(
        int $page,
        int $limit,
        Workspace $workspace
    ) : array {
        return $this->workspaceUserRepository->findByWorkspace($page, $limit, $workspace);
    }

    public function addUser(
        Workspace $workspace,
        AddUserToWorkspaceDTO $dto,
        User $actor
    ) : void {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $dto->email]);

        if(!$user) throw new \DomainException('User not found');

        $workspaceUser = new WorkspaceUser();
        $workspaceUser->setWorkspace($workspace);
        $workspaceUser->setUser($user);
        $workspaceUser->setRole($dto->role ?? '');

        $this->entityManager->persist($workspaceUser);
        $this->entityManager->flush();

        $changes = $this->manageUserAuditLogData($workspace, $workspaceUser);

        $this->dispatcher->dispatch(new WorkspaceAddedUserEvent($workspace, $actor, $changes), 'workspace.user.added');
    }

    public function getCurrentWorkspace(
        int $workspaceId,
        User $user
    ) : Workspace {
        $workspace = $this->workspaceRepository->findOrFail($workspaceId);

        $this->workspaceUserRepository->findByWorkspaceAndUser($workspace, $user);

        return $workspace;
    }

    public function getByIdAndUser(
        int $workspaceId,
        User $user
    ) : Workspace {
        $workspace = $this->workspaceRepository->findOrFail($workspaceId);

        $this->workspaceUserRepository->findByWorkspaceAndUser($workspace, $user);

        return $workspace;
    }

    public function getDeletableForUser(
        int $workspaceId,
        User $user
    ): ?Workspace  {
        $workspace = $this->workspaceRepository->findDeletableForUser($workspaceId, $user);

        if(!$workspace) throw new NotFoundHttpException('Workspace not found');

        return $workspace;
    }

    private function auditLogData(
        Workspace $workspace
    ): array {
        return [
            'name' => $workspace->getName()
        ];
    }

    private function manageUserAuditLogData(
        Workspace $workspace,
        WorkspaceUser $workspaceUser
    ): array {
        return [
            'name' => $workspace->getName(),
            'email' => $workspaceUser->getUser()->getEmail(),
            'role' => $workspaceUser->getRole()
        ];
    }
}
