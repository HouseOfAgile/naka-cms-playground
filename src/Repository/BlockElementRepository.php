<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use App\Entity\BlockElement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockElement|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockElement|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockElement[]    findAll()
 * @method BlockElement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockElement::class);
    }

    // /**
    //  * @return BlockElement[] Returns an array of BlockElement objects
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
    public function findOneBySomeField($value): ?BlockElement
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
