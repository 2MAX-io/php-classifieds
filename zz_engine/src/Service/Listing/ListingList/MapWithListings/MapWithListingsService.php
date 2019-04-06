<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList\MapWithListings;

use App\Enum\ParamEnum;
use App\Helper\StringHelper;
use App\Service\Listing\ListingList\MapWithListings\Dto\MapDefaultConfigDto;
use App\Service\Setting\SettingsDto;
use Symfony\Component\HttpFoundation\Request;

class MapWithListingsService
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    public function getDefaultMapConfig(Request $request): MapDefaultConfigDto
    {
        $mapDefaultConfigDto = new MapDefaultConfigDto();
        $mapDefaultConfigDto->setLatitude($this->settingsDto->getMapDefaultLatitude());
        $mapDefaultConfigDto->setLongitude($this->settingsDto->getMapDefaultLongitude());
        $mapDefaultConfigDto->setZoom($this->settingsDto->getMapDefaultZoom());

        $hasLocationInRequest = !StringHelper::emptyTrim($request->get(ParamEnum::LATITUDE));
        if ($hasLocationInRequest) {
            $mapDefaultConfigDto->setLatitude((float) $request->get(ParamEnum::LATITUDE));
            $mapDefaultConfigDto->setLongitude((float) $request->get(ParamEnum::LONGITUDE));
            $mapDefaultConfigDto->setZoom((int) $request->get(ParamEnum::ZOOM));
        }

        return $mapDefaultConfigDto;
    }
}
