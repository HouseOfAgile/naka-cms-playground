<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use HouseOfAgile\NakaCMSBundle\Entity\PictureGallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PictureGallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method PictureGallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method PictureGallery[]    findAll()
 * @method PictureGallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureGalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PictureGallery::class);
    }

    // /**
    //  * @return PictureGallery[] Returns an array of PictureGallery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PictureGallery
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
