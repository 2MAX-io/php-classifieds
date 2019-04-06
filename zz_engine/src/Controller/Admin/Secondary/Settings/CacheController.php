<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary\Settings;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\System\Cache\SystemClearCacheService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class CacheController extends AbstractAdminController
{
    /**
     * @var SystemClearCacheService
     */
    private $clearCacheService;

    public function __construct(SystemClearCacheService $clearCacheService)
    {
        $this->clearCacheService = $clearCacheService;
    }

    /**
     * @Route("/admin/red5/settings/cache", name="app_admin_cache")
     */
    public function cache(): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/settings/cache.html.twig');
    }

    /**
     * @Route("/admin/red5/settings/cache/clear-cache", name="app_admin_cache_clear", methods={"PATCH"})
     */
    public function cacheClear(Request $request): Response
    {
        $this->denyUnlessAdmin();
        if (!$this->isCsrfTokenValid('csrf_adminCacheClear', $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        $this->clearCacheService->clearAllCaches();

        return $this->redirectToRoute('app_admin_cache');
    }
}
