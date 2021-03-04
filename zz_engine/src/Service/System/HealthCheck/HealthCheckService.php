<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck;

use App\Helper\ExceptionHelper;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class HealthCheckService
{
    /**
     * @var HealthCheckerInterface[]|iterable
     */
    private $healthCheckerList;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param HealthCheckerInterface[]|iterable $healthCheckerList
     */
    public function __construct(iterable $healthCheckerList, TranslatorInterface $trans, LoggerInterface $logger)
    {
        $this->healthCheckerList = $healthCheckerList;
        $this->trans = $trans;
        $this->logger = $logger;
    }

    /**
     * @return string[]
     */
    public function getProblems(): array
    {
        $return = [];

        try {
            foreach ($this->healthCheckerList as $healthChecker) {
                $healthCheckResultDto = $healthChecker->checkHealth();
                if ($healthCheckResultDto->hasProblem()) {
                    $return[] = $healthCheckResultDto->getMessageNotNull();
                }
            }
        } catch (\Throwable $e) {
            $this->logger->critical('error during health check', ExceptionHelper::flatten($e));
            $return[] = $this->trans->trans('trans.Health check failed due to unknown error. If this problem do not correct itself, you must take action.');
        }

        return $return;
    }
}
