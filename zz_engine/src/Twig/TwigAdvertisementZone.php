<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\FilePath;
use App\Service\Advertisement\Dto\AdvertisementDto;
use App\Service\Setting\SettingsDto;
use Twig\Extension\RuntimeExtensionInterface;

class TwigAdvertisementZone implements RuntimeExtensionInterface
{
    /**
     * @var SettingsDto
     */
    public $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    public function advertisementZone(
        string $advertisementZoneName,
        AdvertisementDto $advertisementDto = null
    ): string {
        if (null === $advertisementDto) {
            $advertisementDto = new AdvertisementDto();
        }

        $advertisementDto->defaultAdvertisementZoneId = $this->settingsDto->getDefaultAdvertisementZoneId();
        $path = FilePath::getEngineDir().'/zz_advertisement/advertisement_zone_'.\basename($advertisementZoneName).'.php';
        if (!\file_exists($path)) {
            return '';
        }

        \ob_start();

        /** @noinspection PhpIncludeInspection */
        include $path;

        return \ob_get_clean() ?: '';
    }
}
