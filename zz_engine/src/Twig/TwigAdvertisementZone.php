<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\FilePath;
use App\Service\Advertisement\Dto\AdvertisementDto;
use Twig\Extension\RuntimeExtensionInterface;

class TwigAdvertisementZone implements RuntimeExtensionInterface
{
    /** @noinspection PhpUnusedParameterInspection */
    public function advertisementZone(
        string $advertisementZoneName,
        AdvertisementDto $advertisementDto = null
    ): string {
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
