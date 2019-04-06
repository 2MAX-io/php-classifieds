<?php

declare(strict_types=1);

use App\Helper\StringHelper;
use App\Service\Advertisement\Dto\AdvertisementDto;

/**
 * Template for including advertisements
 * to make it work in production, rename extension from `.dist.php` to `.php`
 * example: advertisement_zone_under_header.php
 */
/** @var AdvertisementDto $advertisementDto */
$zoneId = $advertisementDto->defaultAdvertisementZoneId; // default zone
if (null !== $advertisementDto->category) {
    $zoneId = $advertisementDto->category->getAdvertisementZoneId();
}

if (empty($zoneId)) {
    return; // skip displaying ad
}

?>

<!-- ad zone id: <?php echo StringHelper::escape((string) $zoneId); ?> -->
