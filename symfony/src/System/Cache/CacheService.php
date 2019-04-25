<?php

declare(strict_types=1);

namespace App\System\Cache;

use Psr\SimpleCache\CacheInterface;

class CacheService
{
    public const TWIG_SETTINGS_CACHE = 'twig_settings_cache';

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

//    public function ()
//    {
//        $this->cache->
//    }
}
