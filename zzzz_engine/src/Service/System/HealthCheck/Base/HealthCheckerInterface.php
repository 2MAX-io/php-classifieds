<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\Base;

use App\Service\System\HealthCheck\HealthCheckResultDto;

interface HealthCheckerInterface
{
    public function checkHealth(): HealthCheckResultDto;
}
