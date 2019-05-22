<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

interface UpgradeApi
{
    const CURRENT_VERSION_URL = 'http://upgrade-classifieds.prod.2max.io/version.json.php';
    const UPGRADE_URL = 'http://upgrade-classifieds.prod.2max.io/upgrade.json.php';
    const HEADER_SIGNATURE_BODY_NORMAL_SECURITY = 'X-Signature-Body-Normal-Security';
}
