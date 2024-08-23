<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use Symfony\Contracts\Cache\CacheInterface;

class NakaCacheService
{
    private int $cacheExpirationTime;

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache, int $cacheExpirationTime = 3600)
    {
        $this->cache = $cache;
        $this->cacheExpirationTime = $cacheExpirationTime;
    }

    /**
     * Generates a cache key with an optional random ID component, keeping the base string info.
     *
     * @param string $baseString The base string for the cache key.
     * @param array $identifiers An array of identifiers to make the cache key specific.
     * @param bool $includeRandomId Whether to include a random ID component in the cache key.
     * @return string The generated cache key.
     */
    public function generateCacheKey(string $baseString, array $identifiers = [], bool $includeRandomId = false): string
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

    /**
     * Get the global cache expiration time.
     *
     * @return int Cache expiration time in seconds.
     */
    public function getCacheExpirationTime(): int
    {
        return $this->cacheExpirationTime;
    }

    /**
     * Caches the result of a callable function.
     *
     * @param string $cacheKey The key to identify the cached data.
     * @param callable $dataFetcher The function that fetches the data if it's not cached.
     * @return mixed The cached data or the result of the dataFetcher callable.
     */
    public function cacheResult(string $cacheKey, callable $dataFetcher)
    {
        return $this->cache->get($cacheKey, function ($item) use ($dataFetcher) {
            $item->expiresAfter($this->getCacheExpirationTime());
            return $dataFetcher();
        });
    }

    /**
     * Clears the cache for a specific cache key.
     *
     * @param string $cacheKey The key of the cache item to delete.
     * @return bool Returns true if the cache item was successfully deleted, false otherwise.
     */
    public function clearCacheForKey(string $cacheKey): bool
    {
        return $this->cache->delete($cacheKey);
    }
}
