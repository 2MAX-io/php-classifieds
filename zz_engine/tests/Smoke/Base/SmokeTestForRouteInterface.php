<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Base;

interface SmokeTestForRouteInterface
{
    /**
     * @return string[]
     */
    public static function getRouteNames(): array;
}
