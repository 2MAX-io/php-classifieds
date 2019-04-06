<?php

declare(strict_types=1);

namespace App\Service\Advertisement\Dto;

use App\Entity\Category;

class AdvertisementDto
{
    /** @var int|string|null */
    public $zoneId;

    /** @var Category|null */
    public $category;

    /** @var int|string|null */
    public $defaultAdvertisementZoneId;
}
