<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Enum\AppCacheEnum;
use App\Service\Setting\EnvironmentService;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use App\Service\System\Upgrade\VersionCheckService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpgradeHealthCheckerService implements HealthCheckerInterface
{
    /**
     * @var VersionCheckService
     */
    private $versionCheckService;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        VersionCheckService $versionCheckService,
        EnvironmentService $environmentService,
        CacheInterface $cache,
        TranslatorInterface $trans
    ) {
        $this->versionCheckService = $versionCheckService;
        $this->environmentService = $environmentService;
        $this->cache = $cache;
        $this->trans = $trans;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if ($this->environmentService->getUpgradeAvailableCheckDisabled()) {
            return new HealthCheckResultDto(false);
        }

        if ($this->canUpgrade()) {
            return new HealthCheckResultDto(
                true,
                $this->trans->trans('trans.New version has been released. We recommend updating to the latest version.')
            );
        }

        return new HealthCheckResultDto(false);
    }

    private function canUpgrade(): bool
    {
        return $this->cache->get(AppCacheEnum::ADMIN_UPGRADE_CHECK, function (ItemInterface $item) {
            $item->expiresAfter(3600 * 16);

            return $this->versionCheckService->canUpgrade();
        });
    }
}
