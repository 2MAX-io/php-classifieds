<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Service\Log\PoliceLogIpService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingPoliceLogController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/police-log/{id}", name="app_admin_police_log")
     */
    public function policeLog(Listing $listing, PoliceLogIpService $policeLogIpService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/listing/other/listing_police_log.html.twig', [
            'listing' => $listing,
            'policeLogText' => $policeLogIpService->prepareOutput($listing),
        ]);
    }
}
