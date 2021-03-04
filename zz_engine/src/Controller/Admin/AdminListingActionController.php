<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Listing;
use App\Helper\DateHelper;
use App\Service\Admin\Listing\NextListingWaitingActivationService;
use App\Service\Admin\ListingAction\ListingActionService;
use App\Service\System\Routing\RefererService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class AdminListingActionController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/listing/{id}/action/activate", name="app_admin_listing_activate", methods={"PATCH"})
     */
    public function activate(
        Request $request,
        Listing $listing,
        ListingActionService $listingActionService,
        RefererService $refererService,
        NextListingWaitingActivationService $nextListingWaitingActivationService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminActivateListing'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listingActionService->activate([$listing->getIdNotNull()]);
        $this->em->flush();

        if ($request->get('redirectToNextWaiting')) {
            return $nextListingWaitingActivationService->nextWaitingRedirectResponse();
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/admin/red5/listing/{id}/action/remove-listing", name="app_admin_listing_remove", methods={"DELETE"})
     */
    public function remove(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminRemoveListing'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setAdminRemoved(true);
        $this->em->flush();

        return $refererService->redirectToRefererResponse();
    }

    /**
     * move to the top on lists
     *
     * @Route("/admin/red5/listing/{id}/action/pull-up", name="app_admin_listing_pull_up", methods={"PATCH"})
     */
    public function pullUp(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminPullUpListing'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setOrderByDate(DateHelper::create());
        $this->em->flush();

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/admin/red5/listing/{id}/action/feature-for-week", name="app_admin_listing_feature_for_week", methods={"PATCH"})
     */
    public function featureForWeek(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminFeatureForWeekListing'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setOrderByDate(DateHelper::create());
        $listing->setFeatured(true);
        if ($listing->getFeaturedUntilDate() > DateHelper::create()) {
            $listing->setFeaturedUntilDate(Carbon::instance($listing->getFeaturedUntilDate())->addDays(7));
        } else {
            $listing->setFeaturedUntilDate(DateHelper::carbonNow()->addDays(7));
        }
        $this->em->flush();

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route(
     *     "/admin/red5/listing/action/redirect-to-next-listing-waiting-activation",
     *     name="app_admin_listing_redirect_next_waiting_activation",
     *     methods={"PATCH"},
     * )
     */
    public function redirectToNextListingWaitingActivation(
        Request $request,
        NextListingWaitingActivationService $nextListingWaitingActivationService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_adminRedirectNextWaitingActivation', $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        return $nextListingWaitingActivationService->nextWaitingRedirectResponse();
    }
}
