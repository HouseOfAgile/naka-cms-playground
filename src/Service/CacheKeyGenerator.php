<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

class CacheKeyGenerator
{
    /**
     * Generates a cache key with an optional random ID component, keeping the base string info.
     *
     * @param string $baseString The base string for the cache key.
     * @param array $identifiers An array of identifiers to make the cache key specific.
     * @param bool $includeRandomId Whether to include a random ID component in the cache key.
     * @return string The generated cache key.
     */
    public function generate(string $baseString, array $identifiers = [], bool $includeRandomId = false): string
    {
        $keyComponents = [$baseString];
        if (!empty($identifiers)) {
            $keyComponents = array_merge($keyComponents, $identifiers);
        }
        $cacheKey = implode('_', array_map('strval', $keyComponents));

        if ($includeRandomId) {
            $randomId = bin2hex(random_bytes(8));
            $cacheKey .= "_rand_{$randomId}";
        }

        return $cacheKey;
    }
}
