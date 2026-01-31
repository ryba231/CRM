<?php

namespace App\Repository\Workspace;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use App\Entity\Workspace\WorkspaceUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<WorkspaceUser>
 */
class WorkspaceUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkspaceUser::class);
    }

    public function findOrFail(
        int $id
    ): WorkspaceUser {
        $workspaceUser = $this->find($id);
        if(!$workspaceUser) throw new NotFoundHttpException('Workspace user not found');

        return $workspaceUser;
    }

    public function findByWorkspaceAndUser(
        Workspace $workspace,
        User $user
    ) : WorkspaceUser {
        $workspaceUser = $this->findOneBy(
            [
                'workspace' => $workspace,
                'user' => $user
            ]
        );

        if(!$workspaceUser) throw new NotFoundHttpException('Access denied to workspace');
        
        return $workspaceUser; 
    }

    public function findByUserPaginated(
        int $page,
        int $limit,
        User $user,
        ?bool $includeDeleted = false
    ) : array {
        $qb = $this->createQueryBuilder('wu')
            ->join('wu.workspace', 'w')
            ->andWhere('wu.user = :user')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('user', $user);

        if(!$includeDeleted) {
            $qb->andWhere('w.deleted_at IS NULL');
        }

        $paginator = new Paginator($qb);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total
        ];
    }

    public function findByWorkspace(
        int $page,
        int $limit,
        Workspace $workspace
    ) : array {
        $qb = $this->createQueryBuilder('wu')
            ->join('wu.user', 'u')
            ->andWhere('wu.workspace = :workspace')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('workspace', $workspace);
            
        $paginator = new Paginator($qb);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total
        ];
    }

    //    /**
    //     * @return WorkspaceUser[] Returns an array of WorkspaceUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WorkspaceUser
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
