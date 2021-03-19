<?php

declare(strict_types=1);

use App\Helper\StringHelper;
use App\Service\Advertisement\Dto\AdvertisementDto;

/** @var AdvertisementDto $advertisementDto */
$zoneId = $advertisementDto->defaultAdvertisementZoneId; // default zone
if (null !== $advertisementDto->category) {
    $zoneId = $advertisementDto->category->getAdvertisementZoneId();
}

//if (empty($zoneId)) {
//    return; // skip displaying ad
//}

?>

<!-- ad zone id: <?php echo StringHelper::escape((string) $zoneId); ?> -->

<!-- DO NOT REMOVE INFORMATION BELLOW WITHOUT HAVING VALID LICENSE -->
<!-- information bellow can only be removed when having valid license for 2MAX.io PHP Classified Ads -->
<div class="container-fluid container-lg">
    <div class="alert alert-dark my-4">
        <h1>Preview version of 2MAX.io PHP Classified Ads</h1>
        <div class="mb-2">for commercial use you need to buy license</div>
        <a href="https://php-classified-ads.2max.io/">Buy license for 2MAX.io PHP Classified Ads here</a>
    </div>
</div>
