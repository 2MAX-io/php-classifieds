<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\AdminListingSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingSearchController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/search", name="app_admin_listing_search")
     */
    public function search(
        Request $request,
        AdminListingSearchService $listingSearchService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingSearchService->getList((int) $request->query->get('page', 1));

        return $this->render('admin/listing/listing_search.html.twig', [
            'listings' => $adminListingListDto->getResults(),
            'pager' => $adminListingListDto->getPager(),
        ]);
    }
}
