<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

trait RandomEntityTrait
{
    /**
     * Fetches a random entity instance. Needs beberlei doctrine extensions
     *
     * @return object|null Returns one random entity object or null if none found
     */
    public function findRandom()
    {
        return $this->createQueryBuilder('e')
                    ->orderBy('RAND()')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
