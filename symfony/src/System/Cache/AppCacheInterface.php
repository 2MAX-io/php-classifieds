<?php

declare(strict_types=1);

namespace App\System\Cache;

interface AppCacheInterface
{
    public const ADMIN_STATS_USERS_COUNT = 'ADMIN_STATS_USERS_COUNT';
    public const ADMIN_STATS_VIEWS_COUNT = 'ADMIN_STATS_VIEWS_COUNT';
    public const ADMIN_STATS_LISTINGS_COUNT = 'ADMIN_STATS_LISTINGS_COUNT';
}
