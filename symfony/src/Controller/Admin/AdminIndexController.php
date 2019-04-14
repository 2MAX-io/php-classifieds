<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminIndexController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/index", name="app_admin_index")
     * @Route("/admin/red5/")
     */
    public function adminIndex(): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/index.html.twig', [
        ]);
    }
}
