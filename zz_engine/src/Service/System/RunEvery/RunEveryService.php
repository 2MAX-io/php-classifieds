<?php

declare(strict_types=1);

namespace App\Service\System\RunEvery;

use Psr\SimpleCache\CacheInterface;

class RunEveryService
{
    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function runEvery(callable $function, string $name, int $seconds = null): void
    {
        if (!$this->cache->get($name, false)) {
            $function();
            $this->cache->set($name, true, $seconds);
        }
    }

    public function canRunAgain(string $name, int $seconds = null): bool
    {
        if (!$this->cache->get($name, false)) {
            $this->cache->set($name, true, $seconds);
            return true;
        }

        return false;
    }
}
