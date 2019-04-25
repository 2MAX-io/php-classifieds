<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ListingConfirmListService;
use App\Service\System\Pagination\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingConfirmController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/confirm", name="app_admin_listing_confirm_list")
     */
    public function listingConfirmList(
        Request $request,
        ListingConfirmListService $listingConfirmListService,
        PaginationService $paginationService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingConfirmListService->getToConfirmListingList(
            (int) $request->query->get('page', 1)
        );

        return $this->render('admin/listing/listing_confirm.html.twig', [
            'listings' => $adminListingListDto->getResults(),
            'pagination' => $paginationService->getPaginationHtml($adminListingListDto->getPager()),
            'pager' => $adminListingListDto->getPager(),
        ]);
    }
}
