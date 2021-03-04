<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\ExecuteAction\ExecuteActionDto;
use App\Form\Admin\ExecuteAction\ExecuteActionType;
use App\Service\Admin\Listing\AdminListingSearchService;
use App\Service\Admin\Listing\ExecuteActionOnFiltered\ExecuteActionOnFilteredService;
use App\Service\System\Routing\RefererService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingExecuteActionOnFilteredController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/search/execute-action-on-filtered", name="app_admin_listing_execute_on_filtered")
     */
    public function executeActionOnFilteredListings(
        Request $request,
        ExecuteActionOnFilteredService $executeActionOnFilteredService,
        AdminListingSearchService $listingSearchService,
        RefererService $refererService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingSearchService->getAdminListingListDtoFromRequest($request);
        $executeActionDto = new ExecuteActionDto();
        $executeActionDto->setAdminListingListDto($adminListingListDto);
        $form = $this->createForm(ExecuteActionType::class, $executeActionDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $action = $executeActionDto->getAction();

            if (ExecuteActionType::ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET === $action) {
                $executeActionOnFilteredService->addCustomField($executeActionDto);
            }

            if (ExecuteActionType::ACTION_SET_CATEGORY === $action) {
                $executeActionOnFilteredService->setCategory($executeActionDto);
            }

            return $refererService->redirectToRefererResponse();
        }

        return $this->render('admin/listing/execute_on_filtered/execute_on_filtered.html.twig', [
            'form' => $form->createView(),
            'affectedCount' => $executeActionOnFilteredService->getAffectedCount($executeActionDto),
            'affectedPercentage' => $executeActionOnFilteredService->getAffectedPercentage($executeActionDto) * 100,
        ]);
    }
}
