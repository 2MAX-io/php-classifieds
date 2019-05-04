<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ListingActivateListService;
use App\Service\System\Pagination\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingActivateController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/activate", name="app_admin_listing_activate_list")
     */
    public function listingActivateList(
        Request $request,
        ListingActivateListService $listingActivateListService,
        PaginationService $paginationService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingActivateListService->getToActivateListingList(
            (int) $request->query->get('page', 1)
        );

        return $this->render('admin/listing/listing_activate.html.twig', [
            'listings' => $adminListingListDto->getResults(),
            'pagination' => $paginationService->getPaginationHtml($adminListingListDto->getPager()),
            'pager' => $adminListingListDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/listing/activate/action-for-selected", name="app_admin_listing_activate_action_for_selected")
     */
    public function actionForSelected(Request $request): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('activationActionForSelected', $request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF token not valid');
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
