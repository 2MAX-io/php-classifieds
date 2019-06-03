<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use App\Service\System\Upgrade\VersionCheckService;
use App\System\Cache\AppCacheEnum;
use App\System\EnvironmentService;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpgradeHealthChecker implements HealthCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var VersionCheckService
     */
    private $versionCheckService;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    public function __construct(
        VersionCheckService $versionCheckService,
        EnvironmentService $environmentService,
        CacheInterface $cache,
        TranslatorInterface $trans
    ) {
        $this->trans = $trans;
        $this->versionCheckService = $versionCheckService;
        $this->cache = $cache;
        $this->environmentService = $environmentService;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if ($this->environmentService->getUpgradeAvailableCheckDisabled()) {
            return new HealthCheckResultDto(false);
        }

        if ($this->canUpgrade()) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.New version has been released. We recommend updating to the latest version.'));
        }

        return new HealthCheckResultDto(false);
    }

    private function canUpgrade(): bool
    {
        $cacheName = AppCacheEnum::ADMIN_UPGRADE_CHECK;
        if ($this->cache->has($cacheName)) {
            return $this->cache->get($cacheName);
        }

        $return = $this->versionCheckService->canUpgrade();
        $this->cache->set($cacheName, $return, 3600*16);

        return $return;
    }
}
