<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Other\AdminStatsService;
use App\Service\System\HealthCheck\HealthCheckService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminIndexController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/dashboard", name="app_admin_index")
     * @Route("/admin/red5/")
     */
    public function adminIndexDashboard(
        AdminStatsService $adminStatsService,
        HealthCheckService $healthCheckService
    ): Response {
        $this->denyUnlessAdmin();

        return $this->render('admin/index.html.twig', [
            'adminStats' => $adminStatsService,
            'healthCheckService' => $healthCheckService,
        ]);
    }
}
