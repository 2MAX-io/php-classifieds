<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\ExecuteAction\ApplyCustomFieldType;
use App\Service\Admin\Listing\ExecuteActionOnFiltered\ExecuteActionOnFilteredService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingExecuteActionOnFilteredController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/execute-action-on-filtered", name="app_admin_listing_execute_on_filtered")
     */
    public function executeActionOnFiltered(Request $request, ExecuteActionOnFilteredService $executeActionOnFilteredService): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(ApplyCustomFieldType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $executeActionOnFilteredService->addCustomField($form->get(ApplyCustomFieldType::CUSTOM_FIELD_OPTION)->getData());

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('admin/listing/execute_on_filtered/execute_on_filtered.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
