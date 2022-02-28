<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use HouseOfAgile\NakaCMSBundle\Entity\PageBlockElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageBlockElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageBlockElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageBlockElement[]    findAll()
 * @method PageBlockElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageBlockElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBlockElement::class);
    }

    // /**
    //  * @return PageBlockElement[] Returns an array of PageBlockElement objects
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
    public function findOneBySomeField($value): ?PageBlockElement
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
