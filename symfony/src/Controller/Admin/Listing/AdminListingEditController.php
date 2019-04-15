<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminListingRestrictedType;
use App\Form\Admin\AdminListingType;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingEditController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/edit/{id}", name="app_admin_listing_edit")
     */
    public function adminListingEdit(Request $request, Listing $listing, CustomFieldsForListingFormService $customFieldsForListingFormService): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field'));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_listing_edit', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/admin_listing_edit.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }

    /**
     * @Route("/admin/red5/listing/edit/advanced/{id}", name="app_admin_listing_edit_advanced")
     */
    public function adminListingEditAdvanced(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingRestrictedType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_admin_listing_edit_advanced', [
                'id' => $listing->getId(),
            ]);
        }

        return $this->render('admin/listing/admin_listing_edit_advanced.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
        ]);
    }
}
