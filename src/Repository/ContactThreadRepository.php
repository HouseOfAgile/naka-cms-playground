<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ContactThread;

/**
 * @extends ServiceEntityRepository<ContactThread>
 *
 * @method ContactThread|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactThread|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactThread[]    findAll()
 * @method ContactThread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactThread::class);
    }

    //    /**
    //     * @return ContactThread[] Returns an array of ContactThread objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ContactThread
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
