<?php

declare(strict_types=1);

namespace App\System\Cache;

interface AppCacheEnum
{
    public const ADMIN_STATS_USERS_COUNT = 'ADMIN_STATS_USERS_COUNT';
    public const ADMIN_STATS_VIEWS_COUNT = 'ADMIN_STATS_VIEWS_COUNT';
    public const ADMIN_STATS_LISTINGS_COUNT = 'ADMIN_STATS_LISTINGS_COUNT';
    public const ADMIN_UPGRADE_CHECK = 'ADMIN_UPGRADE_CHECK';
    public const ADMIN_SECURITY_CHECK = 'ADMIN_SECURITY_CHECK';
    public const CRON_EXPIRE_FEATURED = 'CRON_EXPIRE_FEATURED';
    public const CRON_DEACTIVATE_EXPIRED = 'CRON_DEACTIVATE_EXPIRED';
    public const CRON_SET_MAIN_IMAGE = 'CRON_SET_MAIN_IMAGE';
    public const CRON_OPEN_INDEX = 'CRON_OPEN_INDEX';
}
