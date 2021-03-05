<?php

declare(strict_types=1);

use App\Helper\StringHelper;
use App\Service\Advertisement\Dto\AdvertisementDto;

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
