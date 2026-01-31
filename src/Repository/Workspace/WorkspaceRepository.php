<?php

namespace App\Repository\Workspace;

use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Workspace>
 */
class WorkspaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workspace::class);
    }

    public function findOrFail(int $id) : Workspace {
        $workspace = $this->find($id);

        if(!$workspace) throw new NotFoundHttpException('Workspace not found');

        return $workspace;
    }

    public function findDeletableForUser(
        int $workspaceId,
        User $user,
        ?bool $includeDeleted = false
    ) : ?Workspace {
        
        $qb = $this->createQueryBuilder('w')
            ->join('w.memberships', 'wu')
            ->andWhere('wu.user = :user')
            ->andWhere('wu.role = :role')
            ->andWhere('w.id = :id')
            ->setParameter('user', $user)
            ->setParameter('role', 'owner')
            ->setParameter('id', $workspaceId);
        
        if(!$includeDeleted) {
            $qb->andWhere('w.deleted_at IS NULL');
        }
            
        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    public function findExpiredSoftDeleted(
        \DateTimeImmutable $before
    ) : array {
        return $this->createQueryBuilder('w')
            ->andWhere('w.deleted_at IS NOT NULL')
            ->andWhere('w.deleted_at < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Workspace[] Returns an array of Workspace objects
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

    //    public function findOneBySomeField($value): ?Workspace
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
