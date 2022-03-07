<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use App\Entity\BlockElementType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockElementType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockElementType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockElementType[]    findAll()
 * @method BlockElementType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockElementTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockElementType::class);
    }

    // /**
    //  * @return BlockElementType[] Returns an array of BlockElementType objects
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
    public function findOneBySomeField($value): ?BlockElementType
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
