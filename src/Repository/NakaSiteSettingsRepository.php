<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

use App\Entity\NakaSiteSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NakaSiteSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method NakaSiteSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method NakaSiteSettings[]    findAll()
 * @method NakaSiteSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NakaSiteSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NakaSiteSettings::class);
    }

}
