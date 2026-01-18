<?php

namespace App\Repository\Contact;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use App\Entity\Workspace\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function findByOwnerPaginated(
        int $page,
        int $limit,
        User $owner
    ) : array {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.owner = :owner')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->setParameter('owner', $owner);

        $paginator = new Paginator($qb);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total
        ];
    }

    public function findOrFail(
        int $id
    ) : Contact {
        $contact = $this->find($id);
        
        if(!$contact)
        {
            throw new NotFoundHttpException('Contact not found');
        }

        return $contact;
    }

    public function findByWorkspacePaginated(
        Workspace $workspace,
        int $page,
        int $limit,
        ?string $search,
        ?bool $includeDeleted = false
    ) : array {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.workspace = :workspace')
            ->setParameter('workspace', $workspace);

        if(!$includeDeleted) {
            $qb->andWhere('c.deleted_at IS NULL');
        }

        if($search) {
            $qb->andWhere('(c.first_name LIKE :s OR c.last_name LIKE :s)')
                ->setParameter('s', "%$search%");
        }

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total
        ];
    }

    public function findExpiredSoftDeleted(
        \DateTimeImmutable $before
    ) : array {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deleted_at IS NOT NULL')
            ->andWhere('c.deleted_at < :before')
            ->setParameter('before', $before)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Contact[] Returns an array of Contact objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contact
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
