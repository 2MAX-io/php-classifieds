<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck;

use App\Service\System\HealthCheck\Base\HealthCheckerInterface;

class HealthCheckService
{
    /**
     * @var HealthCheckerInterface[]|iterable
     */
    private $healthCheckerList;

    public function __construct(iterable $healthCheckerList)
    {
        $this->healthCheckerList = $healthCheckerList;
    }

    public function getProblems(): array
    {
        $return = [];
        foreach ($this->healthCheckerList as $healthChecker) {
            $healthCheckResultDto = $healthChecker->checkHealth();
            if ($healthCheckResultDto->isProblem()) {
                $return[] = $healthCheckResultDto->getMessage();
            }
        }

        return $return;
    }
}
