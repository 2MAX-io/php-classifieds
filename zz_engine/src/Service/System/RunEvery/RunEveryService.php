<?php

declare(strict_types=1);

namespace App\Service\System\RunEvery;

use Psr\Cache\CacheItemPoolInterface;

class RunEveryService
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function canRunAgain(string $name, int $seconds = null): bool
    {
        $cacheName = $name.'_CAN_RUN_AGAIN';
        $cacheItem = $this->cache->getItem($cacheName);
        if ($cacheItem->isHit()) {
            return false;
        }

        $cacheItem->expiresAfter($seconds);
        $cacheItem->set(false);
        $this->cache->save($cacheItem);

        return true;
    }
}
