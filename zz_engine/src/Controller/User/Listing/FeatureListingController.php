<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Exception\UserVisibleException;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Listing\Featured\FeaturedPackageService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureListingController extends AbstractUserController
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
            'displayUnderHeaderAdvert' => false,
            'listing' => $listing,
            'packages' => $featuredPackageService->getPackages($listing),
            'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUserOrNull()),
        ]);
    }

    /**
     * @Route("/user/feature/make-featured/as-demo/{id}", name="app_user_feature_listing_as_demo", methods={"PATCH"})
     */
    public function makeFeaturedAsDemo(
        Request $request,
        Listing $listing,
        FeaturedListingService $featuredListingService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_featureAsDemo'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $featuredListingService->makeFeaturedAsDemo($listing);
        $this->em->flush();

        return $this->redirectToRoute('app_user_feature_listing', [
            'id' => $listing->getId(),
            'demoStarted' => 1,
        ]);
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
        SettingsService $settingsService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_feature'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        if ($featuredPackage->getRemoved()) {
            throw new UserVisibleException('Featured package has been removed');
        }
        if (!$featuredListingService->isPackageForListingCategory($listing, $featuredPackage)) {
            throw new UserVisibleException('trans.This featured package is not intended for the current category of this listing');
        }

        if ($featuredListingService->hasAmount($listing, $featuredPackage)) {
            $userBalanceChange = $featuredListingService->makeFeaturedByBalance($listing, $featuredPackage);
            $userBalanceChange->setDescription($trans->trans('trans.Featuring of listing: %listingTitle%, using package: %featuredPackageName%', [
                '%listingTitle%' => $listing->getTitle(),
                '%featuredPackageName%' => $featuredPackage->getName(),
            ]));
            $this->em->flush();
        } else {
            if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
                throw new UserVisibleException('trans.Payments have been disabled');
            }
            $paymentDto = $paymentService->createPaymentForFeaturedPackage($listing, $featuredPackage);
            $this->em->flush();

            return $this->redirect($paymentDto->getMakePaymentUrl());
        }

        return $this->redirectToRoute('app_user_feature_listing', [
            'id' => $listing->getId(),
        ]);
    }
}
