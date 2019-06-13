<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Service\Admin\ListingAction\ListingActionService;
use App\Service\System\Routing\RefererService;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminListingActionController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/listing/action/activate/{id}", name="app_admin_listing_activate", methods={"PATCH"})
     */
    public function activate(
        Request $request,
        Listing $listing,
        ListingActionService $listingActionService,
        RefererService $refererService
    ): Response {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminActivate'.$listing->getId(), $request->request->get('_token'))) {
            $listingActionService->activate([$listing]);
            $this->getDoctrine()->getManager()->flush();
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/admin/red5/listing/action/remove/{id}", name="app_admin_listing_remove", methods={"DELETE"})
     */
    public function remove(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRemove'.$listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setAdminRemoved(true);
            $em->flush();
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/admin/red5/listing/action/raise/{id}", name="app_admin_listing_raise", methods={"PATCH"})
     */
    public function raise(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminRaise'.$listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setOrderByDate(new \DateTime());
            $em->flush();
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/admin/red5/listing/action/feature-for-week/{id}", name="app_admin_listing_feature_for_week", methods={"PATCH"})
     */
    public function featureForWeek(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if ($this->isCsrfTokenValid('adminFeatureForWeek'.$listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $listing->setOrderByDate(new \DateTime());
            $listing->setFeatured(true);
            if ($listing->getFeaturedUntilDate() > new \DateTime()) {
                $listing->setFeaturedUntilDate(Carbon::instance($listing->getFeaturedUntilDate())->addDays(7));
            } else {
                $listing->setFeaturedUntilDate(Carbon::now()->addDays(7));
            }
            $em->flush();
        }

        return $refererService->redirectToRefererResponse();
    }
}
