<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use App\Entity\AdBanner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdBanner>
 *
 * @method AdBanner|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdBanner|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdBanner[]    findAll()
 * @method AdBanner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdBannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdBanner::class);
    }

    //    /**
    //     * @return AdBanner[] Returns an array of AdBanner objects
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

    //    public function findOneBySomeField($value): ?AdBanner
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
