<?php
namespace App\Service\Workspace;

use App\DTO\Workspace\UpdateUserInWorkspaceDTO;
use App\Entity\User\User;
use App\Entity\Workspace\WorkspaceUser;
use App\Event\WorkspaceDeletedUserEvent;
use App\Event\WorkspaceRoleChangedUserEvent;
use App\Repository\Workspace\WorkspaceUserRepository;
use App\Service\AuditLog\ChangeSetComparator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WorkspaceUserManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WorkspaceUserRepository $workspaceUserRepository,
        private EventDispatcherInterface $dispatcher,
        private ChangeSetComparator $changeSetComparator
    ){}

    public function get(
        int $id
    ) : WorkspaceUser {
        return $this->workspaceUserRepository->findOrFail($id);
    }

    public function updateUser(
        WorkspaceUser $workspaceUser,
        UpdateUserInWorkspaceDTO $dto,
        User $actor
    ) : WorkspaceUser {
        $before = $this->auditLogData($workspaceUser);

        if($dto->role) $workspaceUser->setRole($dto->role);

        $changes = $this->changeSetComparator->diff($before, $this->auditLogData($workspaceUser));
        $this->entityManager->flush();

        if($changes !== []){
            $this->dispatcher->dispatch(new WorkspaceRoleChangedUserEvent($workspaceUser, $actor, $changes), 'workspace.user.role_changed');
        }

        return $workspaceUser;
    }

    public function delete(
        WorkspaceUser $workspaceUser,
        User $actor
    ) : void {
        $this->entityManager->remove($workspaceUser);
        $this->dispatcher->dispatch(new WorkspaceDeletedUserEvent($workspaceUser, $actor, $this->auditLogData($workspaceUser)), 'workspace.user.deleted');
        $this->entityManager->flush();

    }

    private function auditLogData(
        WorkspaceUser $workspaceUser
    ): array {
        return [
            'name' => $workspaceUser->getWorkspace()->getName(),
            'email' => $workspaceUser->getUser()->getEmail(),
            'role' => $workspaceUser->getRole()
        ];
    }
}