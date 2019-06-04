<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Service\Admin\Listing\ListingActivateListService;
use App\Service\Admin\ListingAction\ListingActionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingActivateController extends AbstractAdminController
{
    public const ACTIVATE_ACTION = 'ACTIVATE_ACTION';
    public const REJECT_ACTION = 'REJECT_ACTION';

    /**
     * @Route("/admin/red5/listing/activate/list", name="app_admin_listing_activate_list")
     */
    public function listingActivateList(
        Request $request,
        ListingActivateListService $listingActivateListService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingActivateListService->getToActivateListingList(
            (int) $request->query->get('page', 1)
        );

        return $this->render('admin/listing/listing_activate.html.twig', [
            'listings' => $adminListingListDto->getResults(),
            'pager' => $adminListingListDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/listing/activate/list/action-on-selected", name="app_admin_listing_activate_action_on_selected")
     */
    public function actionForSelected(Request $request, ListingActionService $listingActionService): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('activationActionForSelected', $request->get('_token'))) {
            throw $this->createAccessDeniedException('CSRF token not valid');
        }

        $action = $request->get('action');
        $listingIds = \explode(',', $request->get('selected'));

        if ($action === static::ACTIVATE_ACTION) {
            $listingActionService->activate($listingIds);
        }

        if ($action === static::REJECT_ACTION) {
            $listingActionService->reject($listingIds);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
