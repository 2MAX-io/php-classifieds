<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\ExecuteAction\ExecuteActionType;
use App\Form\Admin\ExecuteAction\ExecuteActionDto;
use App\Service\Admin\Listing\ExecuteActionOnFiltered\ExecuteActionOnFilteredService;
use App\Service\System\Routing\RefererService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingExecuteActionOnFilteredController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/execute-action-on-filtered", name="app_admin_listing_execute_on_filtered")
     */
    public function executeActionOnFiltered(
        Request $request,
        ExecuteActionOnFilteredService $executeActionOnFilteredService,
        RefererService $refererService
    ): Response {
        $this->denyUnlessAdmin();

        $executeActionDto = new ExecuteActionDto();
        $form = $this->createForm(ExecuteActionType::class, $executeActionDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $action = $executeActionDto->getAction();

            if ($action === ExecuteActionType::ACTION_SET_CUSTOM_FIELD_OPTION) {
                $executeActionOnFilteredService->addCustomField($executeActionDto->getCustomFieldOption());
            }

            if ($action === ExecuteActionType::ACTION_SET_CATEGORY) {
                $executeActionOnFilteredService->setCategory($executeActionDto->getCategory());
            }

            return $refererService->redirectToRefererResponse();
        }

        return $this->render('admin/listing/execute_on_filtered/execute_on_filtered.html.twig', [
            'form' => $form->createView(),
            'affectedCount' => $executeActionOnFilteredService->getAffectedCount(),
            'affectedPercentage' => $executeActionOnFilteredService->getAffectedPercentage() * 100,
        ]);
    }
}
