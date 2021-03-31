<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Base;

interface SmokeTestForRouteInterface
{
    public static function getRouteName(): string;
}
