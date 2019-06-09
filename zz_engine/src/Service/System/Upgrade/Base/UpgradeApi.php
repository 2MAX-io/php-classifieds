<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade\Base;

interface UpgradeApi
{
    public const LATEST_VERSION_URL = 'https://classified-upgrade-prod.2max.io/latest-version.json';
    public const UPGRADE_LIST_URL = 'https://classified-upgrade-prod.2max.io/upgrade-list.json';
    public const HEADER_SIGNATURE_NORMAL_SECURITY = 'X-Signature-Normal-Security';
}
