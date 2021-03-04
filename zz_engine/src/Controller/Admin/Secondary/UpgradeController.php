<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Enum\AppCacheEnum;
use App\Exception\UserVisibleException;
use App\Service\Setting\EnvironmentService;
use App\Service\System\Upgrade\Api\UpgradeApiService;
use App\Service\System\Upgrade\Dto\LatestVersionDto;
use App\Service\System\Upgrade\RunUpgradeService;
use App\Service\System\Upgrade\VersionCheckService;
use App\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\Cache\CacheInterface;

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

        /** @var LatestVersionDto $latestVersionDto */
        $latestVersionDto = $versionCheckService->getLatestVersion();

        return $this->render('admin/secondary/upgrade/upgrade.html.twig', [
            'latestVersion' => $latestVersionDto,
            'installedVersion' => new Version(),
            'canUpgrade' => $versionCheckService->canUpgrade(),
        ]);
    }

    /**
     * @Route("/admin/red5/upgrade/execute-upgrade", name="app_admin_upgrade_run")
     */
    public function runUpgrade(
        Request $request,
        RunUpgradeService $runUpgradeService,
        UpgradeApiService $upgradeApiService,
        VersionCheckService $versionCheckService
    ): Response {
        $this->denyUnlessAdmin();
        $this->blockIfUpgradeDisabled();

        if (!$this->isCsrfTokenValid('csrf_adminExecuteUpgrade', $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        if (!$versionCheckService->canUpgrade()) {
            return $this->redirectToRoute('app_admin_upgrade');
        }

        $upgradeArray = $upgradeApiService->getUpgradeList();
        if (null === $upgradeArray) {
            throw new UserVisibleException('trans.Error, could not download upgrade files, upgrade not started');
        }

        $runUpgradeService->runUpgrade($upgradeArray);

        return $this->render('admin/secondary/upgrade/upgrade_executed.html.twig');
    }

    private function blockIfUpgradeDisabled(): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }
    }
}
