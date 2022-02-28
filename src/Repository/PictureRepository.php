<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use HouseOfAgile\NakaCMSBundle\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }

    public function getRandomPicture(): ?Picture
    {
        $qb = $this->createQueryBuilder('f');

        $qb->orderBy('rand()');
        $randomPicture = $qb->getQuery()
            ->getResult();
        return $randomPicture ? $randomPicture[0] : null;
    }
}
