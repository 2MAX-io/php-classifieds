<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminListingEditType;
use App\Form\ListingCustomFieldListType;
use App\Form\ListingType;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use App\Service\Listing\Save\SaveListingService;
use Minwork\Helper\Arr;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingEditController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/edit/{id}", name="app_admin_listing_edit")
     */
    public function adminListingEdit(
        Request $request,
        Listing $listing,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
        SaveListingService $createListingService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingEditType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $customFieldsForListingFormService->saveCustomFieldsToListing(
                $listing,
                Arr::getNestedElement($request->request->all(), [ListingType::LISTING_FIELD, ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD]) ?? []
            );
            $createListingService->saveSearchText($listing);
            $createListingService->updateSlug($listing);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/edit/admin_listing_edit.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}