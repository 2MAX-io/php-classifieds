<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Exception\UserVisibleMessageException;
use App\Service\System\Upgrade\Api\UpgradeApiService;
use App\Service\System\Upgrade\UpgradeService;
use App\Service\System\Upgrade\VersionCheckService;
use App\System\Cache\AppCacheEnum;
use App\System\EnvironmentService;
use App\Version;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpgradeController extends AbstractAdminController
{
    /**
     * @var EnvironmentService
     */
    private $environmentService;

    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * @Route("/admin/red5/upgrade", name="app_admin_upgrade")
     */
    public function upgrade(VersionCheckService $versionCheckService, CacheInterface $cache): Response
    {
        $this->denyUnlessAdmin();
        $this->blockIfUpgradeDisabled();

        $cache->delete(AppCacheEnum::ADMIN_UPGRADE_CHECK);

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', [
            'latestVersion' => $versionCheckService->getLatestVersion(),
            'installedVersion' => new Version(),
            'canUpgrade' => $versionCheckService->canUpgrade(),
        ]);
    }

    /**
     * @Route("/admin/red5/upgrade/execute", name="app_admin_upgrade_execute")
     */
    public function upgradeExecute(
        Request $request,
        UpgradeService $upgradeService,
        UpgradeApiService $upgradeApiService,
        VersionCheckService $versionCheckService
    ): Response {
        $this->denyUnlessAdmin();
        $this->blockIfUpgradeDisabled();

        if (!$this->isCsrfTokenValid('adminExecuteUpgrade', $request->request->get('_token'))) {
            throw new UserVisibleMessageException('CSRF token invalid');
        }

        if (!$versionCheckService->canUpgrade()) {
            return $this->redirectToRoute('app_admin_upgrade');
        }

        $upgradeArr = $upgradeApiService->getUpgradeList();
        if ($upgradeArr === null) {
            throw new UserVisibleMessageException('trans.Upgrade not started');
        }

        $upgradeService->upgrade($upgradeArr);

        return $this->render('admin/secondary/upgrade/upgrade_executed.html.twig');
    }

    private function blockIfUpgradeDisabled(): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleMessageException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }
    }
}
