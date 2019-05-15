<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ExecuteActionOnFiltered\ExecuteActionOnFilteredService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingExecuteActionOnFilteredController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/execute-action-on-filtered", name="app_admin_listing_execute_on_filtered")
     */
    public function executeActionOnFiltered(Request $request, ExecuteActionOnFilteredService $executeActionOnFilteredService): Response
    {
        $this->denyUnlessAdmin();

        $executeActionOnFilteredService->setTitle();

        return $this->render('admin/listing/execute_on_filtered/execute_on_filtered.html.twig', [

        ]);
    }
}
