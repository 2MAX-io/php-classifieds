<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Exception\UserVisibleMessageException;
use App\Service\System\Upgrade\UpgradeApiService;
use App\Service\System\Upgrade\UpgradeService;
use App\Version;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradeController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/upgrade", name="app_admin_upgrade")
     */
    public function upgrade(UpgradeApiService $upgradeApi): Response
    {
        $this->denyUnlessAdmin();

        $latestVersionDto = $upgradeApi->getLatestVersion();

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', [
            'latestVersion' => $latestVersionDto,
            'installedVersion' => new Version(),
            'toUpgrade' => $latestVersionDto && true,
        ]);
    }

    /**
     * @Route("/admin/red5/upgrade/execute", name="app_admin_upgrade_execute")
     */
    public function upgradeExecute(UpgradeService $upgradeService, UpgradeApiService $upgradeApiService): Response
    {
        $this->denyUnlessAdmin();

        $upgradeArr = $upgradeApiService->getUpgrade();

        if ($upgradeArr === null) {
            throw new UserVisibleMessageException('trans.Upgrade not started');
        }

        $upgradeService->upgrade($upgradeArr);

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', []);
    }
}
