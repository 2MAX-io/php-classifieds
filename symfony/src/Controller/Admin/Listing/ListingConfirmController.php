<?php

declare(strict_types=1);

namespace App\Controller\Admin\Listing;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Service\Admin\Listing\ListingConfirmListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingConfirmController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/confirm", name="app_admin_listing_confirm_list")
     */
    public function listingConfirmList(ListingConfirmListService $listingConfirmListService): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/listing/listing_confirm.html.twig', [
            'listings' => $listingConfirmListService->getToConfirmListingList()
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

        return $this->redirectToRoute('app_admin_listing_confirm_list');
    }

    /**
     * @Route("/admin/red5/listing-confirm/delete/{id}", name="app_admin_listing_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Listing $listing): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminDelete'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $listing->setAdminRemoved(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_listing_confirm_list');
    }
}
