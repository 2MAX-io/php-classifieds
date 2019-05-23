<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Exception\UserVisibleMessageException;
use App\Service\System\Upgrade\Api\UpgradeApiService;
use App\Service\System\Upgrade\UpgradeService;
use App\Service\System\Upgrade\VersionCheckService;
use App\Version;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradeController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/upgrade", name="app_admin_upgrade")
     */
    public function upgrade(VersionCheckService $versionCheckService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', [
            'latestVersion' => $versionCheckService->getLatestVersion(),
            'installedVersion' => new Version(),
            'canUpgrade' => $versionCheckService->canUpgrade(),
        ]);
    }

    /**
     * @Route("/admin/red5/upgrade/execute", name="app_admin_upgrade_execute")
     */
    public function upgradeExecute(UpgradeService $upgradeService, UpgradeApiService $upgradeApiService): Response
    {
        $this->denyUnlessAdmin();

        $upgradeArr = $upgradeApiService->getUpgradeList();

        if ($upgradeArr === null) {
            throw new UserVisibleMessageException('trans.Upgrade not started');
        }

        $upgradeService->upgrade($upgradeArr);

        return $this->render('admin/secondary/upgrade/upgrade_executed.html.twig', []);
    }
}
