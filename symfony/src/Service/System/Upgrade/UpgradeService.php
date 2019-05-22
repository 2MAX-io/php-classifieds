<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Helper\FilePath;

class UpgradeService
{
    public function upgrade(array $upgradeArr): void
    {
        foreach ($upgradeArr['upgradeList'] as $upgradeItem) {
            $type = $upgradeItem['type'];

            if ($type === 'file') {

                if (!\file_exists(FilePath::getUpgradeDir())) {
                    \mkdir(FilePath::getUpgradeDir(), 0775);
                }

                $path = FilePath::getUpgradeDir() . '/upgrade.php';
                \file_put_contents($path, \base64_decode($upgradeItem['content']));

                try {
                    $run = include $path;
                    $run();
                } catch (\Throwable $e) {
                    throw $e;
                }
            }
        }
    }
}
