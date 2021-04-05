<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Entity\Package;
use App\Exception\UserVisibleException;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Listing\Featured\PackageService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsDto;
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

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->settingsDto = $settingsDto;
    }

    /**
     * @Route("/user/feature/{id}", name="app_user_feature_listing")
     */
    public function feature(
        Listing $listing,
        PackageService $packageService,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        return $this->render('user/listing/feature_listing.twig', [
            'displayUnderHeaderAdvert' => false,
            'listing' => $listing,
            'packages' => $packageService->getPackages($listing),
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
     *     "/user/feature/make-featured/package/{package}/listing/{id}",
     *     name="app_user_feature_listing_action",
     *     methods={"PATCH"},
     * )
     */
    public function makeFeatured(
        Request $request,
        Listing $listing,
        Package $package,
        FeaturedListingService $featuredListingService,
        PaymentService $paymentService,
        TranslatorInterface $trans
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_feature'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        if ($package->getRemoved()) {
            throw new UserVisibleException('Package has been removed');
        }
        if (!$featuredListingService->isPackageForListingCategory($listing, $package)) {
            throw new UserVisibleException('trans.This package is not intended for the current category of this listing');
        }

        if ($featuredListingService->hasAmount($listing, $package)) {
            $userBalanceChange = $featuredListingService->makeFeaturedByBalance($listing, $package);
            $userBalanceChange->setDescription($trans->trans('trans.Featuring of listing: %listingTitle%, using package: %packageName%', [
                '%listingTitle%' => $listing->getTitle(),
                '%packageName%' => $package->getName(),
            ]));
            $this->em->flush();
        } else {
            if (!$this->settingsDto->isPaymentAllowed()) {
                throw new UserVisibleException('trans.Payments have been disabled');
            }
            $paymentDto = $paymentService->createPaymentForPackage($listing, $package);
            $this->em->flush();

            return $this->redirect($paymentDto->getMakePaymentUrl());
        }

        return $this->redirectToRoute('app_user_feature_listing', [
            'id' => $listing->getId(),
        ]);
    }
}
