<?php

declare(strict_types=1);

namespace App\System\Cache;

interface AppCacheEnum
{
    const ADMIN_STATS_USERS_COUNT = 'ADMIN_STATS_USERS_COUNT';
    const ADMIN_STATS_VIEWS_COUNT = 'ADMIN_STATS_VIEWS_COUNT';
    const ADMIN_STATS_LISTINGS_COUNT = 'ADMIN_STATS_LISTINGS_COUNT';
    const ADMIN_UPGRADE_CHECK = 'ADMIN_UPGRADE_CHECK';
}
