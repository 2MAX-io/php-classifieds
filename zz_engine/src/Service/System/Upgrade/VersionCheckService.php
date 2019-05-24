<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Service\System\Upgrade\Api\UpgradeApiService;
use App\Service\System\Upgrade\Dto\LatestVersionDto;
use App\Version;

class VersionCheckService
{
    /**
     * @var UpgradeApiService
     */
    private $upgradeApiService;

    public function __construct(UpgradeApiService $upgradeApiService)
    {
        $this->upgradeApiService = $upgradeApiService;
    }

    public function canUpgrade(): bool
    {
        $latestVersionDto = $this->upgradeApiService->getLatestVersion();

        return $this->fetchedLatestVersion() && $latestVersionDto->getVersion() > $this->getInstalledVersion();
    }

    public function getLatestVersion(): ?LatestVersionDto
    {
        return $this->upgradeApiService->getLatestVersion();
    }

    public function getInstalledVersion(): int
    {
        return Version::getVersion();
    }

    private function fetchedLatestVersion(): bool
    {
        return $this->upgradeApiService->getLatestVersion() !== null;
    }
}
