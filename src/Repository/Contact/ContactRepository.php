<?php

namespace App\Repository\Contact;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function findPaginatedByOwner(
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
            throw new \RuntimeException('Contact not found');
        }

        return $contact;
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
