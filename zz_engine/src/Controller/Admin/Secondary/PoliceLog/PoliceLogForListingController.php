<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary\PoliceLog;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Service\Listing\Secondary\PoliceLog\PoliceLogForListingService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PoliceLogForListingController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/police-log/listing/{id}/listing-police-log", name="app_admin_police_log_listing")
     */
    public function policeLogForListing(Listing $listing, PoliceLogForListingService $policeLogIpService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/secondary/police_log/police_log_listing.html.twig', [
            'listing' => $listing,
            'policeLogText' => $policeLogIpService->getLogForPolice($listing),
            ParamEnum::DATA_FOR_JS => [
                ParamEnum::POLICE_LOG_TEXT => $policeLogIpService->getLogForPolice($listing),
            ],
        ]);
    }
}
