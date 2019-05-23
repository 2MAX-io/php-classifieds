<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade\Base;

interface UpgradeApi
{
    const LATEST_VERSION_URL = 'http://upgrade-classifieds.prod.2max.io/latest-version.json';
    const UPGRADE_LIST_URL = 'http://upgrade-classifieds.prod.2max.io/upgrade-list.json';
    const HEADER_SIGNATURE_BODY_NORMAL_SECURITY = 'X-Signature-Body-Normal-Security';
}
