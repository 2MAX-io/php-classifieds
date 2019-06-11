<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Exception\UserVisibleMessageException;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Listing\Featured\FeaturedPackageService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use App\Service\System\Routing\RefererService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureListingController extends AbstractUserController
{
    /**
     * @Route("/user/feature/{id}", name="app_user_feature_listing")
     */
    public function feature(
        Listing $listing,
        FeaturedPackageService $featuredPackageService,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        return $this->render('user/listing/feature_listing.twig', [
            'listing' => $listing,
            'packages' => $featuredPackageService->getPackages($listing),
            'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUser()),
        ]);
    }

    /**
     * @Route("/user/feature/make-featured/as-demo/{id}", name="app_user_feature_listing_as_demo", methods={"PATCH"})
     */
    public function makeFeaturedAsDemo(
        Request $request,
        Listing $listing,
        FeaturedListingService $featuredListingService,
        RefererService $refererService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('featureAsDemo'.$listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $featuredListingService->makeFeaturedAsDemo($listing);
            $em->flush();

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $listing->getId(), 'demoStarted' => 1]
            );
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route(
     *     "/user/feature/make-featured/package/{featuredPackage}/listing/{id}",
     *     name="app_user_feature_listing_action",
     *     methods={"PATCH"},
     * )
     */
    public function makeFeatured(
        Request $request,
        Listing $listing,
        FeaturedPackage $featuredPackage,
        FeaturedListingService $featuredListingService,
        PaymentService $paymentService,
        TranslatorInterface $trans,
        SettingsService $settingsService,
        RefererService $refererService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('feature'.$listing->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

            if ($featuredListingService->hasAmount($listing, $featuredPackage)) {
                $userBalanceChange = $featuredListingService->makeFeaturedByBalance($listing, $featuredPackage);
                $userBalanceChange->setDescription($trans->trans('trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%', [
                    '%listingTitle%' => $listing->getTitle(),
                    '%featuredPackageName%' => $featuredPackage->getName(),
                ]));
                $em->flush();
            } else {
                if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
                    throw new UserVisibleMessageException('trans.Payments have been disabled');
                }
                if ($featuredPackage->getRemoved()) {
                    throw new UserVisibleMessageException('Featured package has been removed');
                }

                $paymentDto = $paymentService->createPaymentForFeaturedPackage($listing, $featuredPackage);
                $em->flush();

                return $this->redirect($paymentDto->getPaymentExecuteUrl());
            }

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $listing->getId()]
            );
        }

        return $refererService->redirectToRefererResponse();
    }
}
