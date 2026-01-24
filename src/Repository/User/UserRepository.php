<?php

namespace App\Repository\User;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findPaginated(int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('u')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total
        ];

    }

    public function findByEmail(string $email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOrFail(int $id) : User {
        $user = $this->find($id);

        if(!$user)
        {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    public function findExpiredSoftDeleted(
        \DateTimeImmutable $before
    ) : array {
        return $this->createQueryBuilder('u')
            ->andWhere('u.deleted_at IS NOT NULL')
            ->andWhere('u.anonymized__at IS NULL')
            ->andWhere('u.deleted_at < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }

    public function getSystemUser() : User {
        return $this->find(1) ?? throw new \LogicException('System user missing');
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
