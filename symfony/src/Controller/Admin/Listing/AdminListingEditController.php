<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Form\Admin\AdminListingAdvancedEditType;
use App\Form\Admin\AdminListingEditType;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use App\Service\Listing\Save\CreateListingService;
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
        CreateListingService $createListingService
    ): Response {
        $this->denyUnlessAdmin();

        $form = $this->createForm(AdminListingEditType::class, $listing);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $customFieldsForListingFormService->saveCustomFieldsToListing($listing, $request->request->get('form_custom_field'));
            $createListingService->saveSearchText($listing);
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

        $form = $this->createForm(AdminListingAdvancedEditType::class, $listing);
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

    /**
     * @Route("/admin/red5/listing-confirm/confirm/{id}", name="app_admin_listing_confirm", methods={"PATCH"})
     */
    public function confirm(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminConfirm'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setAdminConfirmed(true);
            $listing->setAdminLastConfirmationDate(new \DateTime());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));;
    }

    /**
     * @Route("/admin/red5/listing-confirm/remove/{id}", name="app_admin_listing_remove", methods={"DELETE"})
     */
    public function remove(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRemove'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setAdminRemoved(true);
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));;
    }

    /**
     * @Route("/admin/red5/listing-confirm/raise/{id}", name="app_admin_listing_raise", methods={"PATCH"})
     */
    public function raise(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRaise'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setOrderByDate(new \DateTime());
            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer'));;
    }
}
