<?php

declare(strict_types=1);

namespace App\Service\System\Cache;

use Symfony\Component\Cache\Adapter\ArrayAdapter;

class RuntimeCacheService extends ArrayAdapter
{
    public function __construct()
    {
        parent::__construct(0, false);
    }
}
