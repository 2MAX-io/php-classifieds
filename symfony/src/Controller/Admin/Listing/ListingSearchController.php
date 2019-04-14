<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ListingSearchService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingSearchController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/search", name="app_admin_listing_search")
     */
    public function search(ListingSearchService $listingSearchService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/listing/listing_search.html.twig', [
            'listings' => $listingSearchService->getListings(),
        ]);
    }
}
