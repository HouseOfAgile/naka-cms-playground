<?php

namespace HouseOfAgile\NakaCMSBundle\Repository;

trait RandomEntityTrait
{
    /**
     * Fetches up to a specified count of random entity instances. Needs beberlei doctrine extensions.
     *
     * @param int $count The number of random entities to fetch
     * @param bool $asArray Whether to force the return value to be an array even if count is 0 or 1
     * @return array Returns an array of random entity objects, or an empty array if none found
     */
    public function findRandom(int $count = 1, bool $asArray = false): array|Object
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->orderBy('RAND()')
            ->setMaxResults($count)
            ->getQuery();

        if ($count <= 1) {
            $result = $queryBuilder->getOneOrNullResult();
            if (!$asArray) {
                return $result;
            } else {
                return $result !== null ? [$result] : [];
            }
        }

        return $queryBuilder->getResult();
    }
}
