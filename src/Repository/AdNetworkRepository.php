<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use App\Entity\AdNetwork;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdNetwork>
 *
 * @method AdNetwork|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdNetwork|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdNetwork[]    findAll()
 * @method AdNetwork[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdNetworkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdNetwork::class);
    }

    //    /**
    //     * @return AdNetwork[] Returns an array of AdNetwork objects
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

    //    public function findOneBySomeField($value): ?AdNetwork
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
