<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\System\Upgrade\UpgradeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradeController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/upgrade", name="app_admin_upgrade")
     */
    public function upgrade(): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', []);
    }

    /**
     * @Route("/admin/red5/upgrade/execute", name="app_admin_upgrade_execute")
     */
    public function upgradeExecute(UpgradeService $upgradeService): Response
    {
        $this->denyUnlessAdmin();

        $upgradeService->upgrade();

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', []);
    }
}
