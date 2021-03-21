<?php

declare(strict_types=1);

/**
 * DO NOT REMOVE INFORMATION BELLOW WITHOUT HAVING VALID LICENSE
 *
 * to include real advertisements when having proper license,
 * see advertisement_zone_under_header.dist.php and other *.dist.php
 * files in this directory for more information
 */
use App\Twig\TwigAdvertisementZone;

/** @var TwigAdvertisementZone $this */
if (!empty($this->settingsDto->getLicense())) {
    return;
}

?>

<!-- DO NOT REMOVE INFORMATION BELLOW WITHOUT HAVING VALID LICENSE -->
<!-- information bellow can only be removed when having valid license for 2MAX.io PHP Classified Ads -->
<!-- no commercial and for profit use including display of advertisement allowed until license has been obtained -->
<div class="container-fluid container-lg">
    <div class="alert alert-dark my-4">
        <h2 class="d-none d-lg-inline">Preview version of 2MAX.io PHP Classified Ads</h2>

        <div class="d-none d-lg-block mb-2">no commercial and for profit use including display of advertisement allowed until license has been obtained</div>

        <a href="https://php-classified-ads.2max.io/">
            Buy license for 2MAX.io PHP Classified Ads here
        </a>
    </div>
</div>
