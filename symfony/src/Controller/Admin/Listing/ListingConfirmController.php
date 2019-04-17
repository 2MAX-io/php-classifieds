<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ListingConfirmListService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingConfirmController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/confirm", name="app_admin_listing_confirm_list")
     */
    public function listingConfirmList(ListingConfirmListService $listingConfirmListService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/listing/listing_confirm.html.twig', [
            'listings' => $listingConfirmListService->getToConfirmListingList()
        ]);
    }
}
