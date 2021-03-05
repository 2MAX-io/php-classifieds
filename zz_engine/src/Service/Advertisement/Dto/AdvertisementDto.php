<?php

declare(strict_types=1);

namespace App\Service\Advertisement\Dto;

use App\Entity\Category;

class AdvertisementDto
{
    /** @var null|int|string */
    public $zoneId;

    /** @var null|Category */
    public $category;

    /** @var null|int|string */
    public $defaultAdvertisementZoneId;
}
