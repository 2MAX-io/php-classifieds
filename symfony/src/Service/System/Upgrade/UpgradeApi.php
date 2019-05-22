<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

interface UpgradeApi
{
    const CURRENT_VERSION_URL = 'http://upgrade-classifieds.prod.2max.io/version.json';
    const UPGRADE_URL = 'http://upgrade-classifieds.prod.2max.io/upgrade.json.php';
}
