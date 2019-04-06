<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Entity\Package;
use App\Exception\UserVisibleException;
use App\Form\Listing\Feature\FeatureDto;
use App\Form\Listing\Feature\FeatureType;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeatureListingByPackageService;
use App\Service\Listing\Package\PackagesForListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
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

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(
        SettingsDto $settingsDto,
        CsrfTokenManagerInterface $csrfTokenManager,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->settingsDto = $settingsDto;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/user/feature/{id}", name="app_user_feature_listing")
     */
    public function feature(
        Request $request,
        Listing $listing,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $featureDto = new FeatureDto();
        $form = $this->createForm(FeatureType::class, $featureDto, [
            'listing' => $listing,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_user_feature_listing_pay', [
                'id' => $listing->getId(),
                'package' => $featureDto->getPackageNotNull()->getId(),
                '_token' => $this->csrfTokenManager->getToken('csrf_feature'.$listing->getId())->getValue(),
            ]);
        }

        return $this->render('user/listing/feature_listing.twig', [
            'displayUnderHeaderAdvert' => false,
            'form' => $form->createView(),
            'listing' => $listing,
            'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUserOrNull()),
        ]);
    }

    /**
     * @Route(
     *     "/user/feature/make-featured/listing/{id}/package/{package}",
     *     name="app_user_feature_listing_pay",
     * )
     * @Route(
     *     "/user/feature/make-featured/listing/{id}/package/{package}/confirm-pay-by-balance",
     *     name="app_user_feature_listing_pay_by_balance_confirm",
     * )
     */
    public function payForListingFeatured(
        Request $request,
        Listing $listing,
        Package $package,
        FeatureListingByPackageService $featureListingByPackageService,
        PaymentService $paymentService,
        PackagesForListingService $packagesForListingService,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService,
        TranslatorInterface $trans
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_feature'.$listing->getId(), $request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        if ($package->getRemoved()) {
            throw new UserVisibleException('Package has been removed');
        }
        if (!$packagesForListingService->isPackageForListing($listing, $package)) {
            throw new UserVisibleException('trans.This package is not intended for the current category of this listing');
        }
        if (!$package->isPaidPackage()) {
            $featureListingByPackageService->makeFeaturedFree($listing, $package);
            $this->em->flush();

            if ($package->getDemoPackage()) {
                return $this->redirectToRoute('app_user_feature_listing', [
                    'id' => $listing->getId(),
                    'demoStarted' => 1,
                ]);
            }

            return $this->redirectToRoute('app_user_feature_listing', [
                'id' => $listing->getId(),
            ]);
        }

        if ($userBalanceService->hasAmount($package->getPrice(), $currentUserService->getUser())) {
            if ('app_user_feature_listing_pay_by_balance_confirm' !== $request->get('_route')) {
                return $this->render('user/listing/feature_listing_by_balance_confirm.twig', [
                    'displayUnderHeaderAdvert' => false,
                    'package' => $package,
                    'listing' => $listing,
                    'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUserOrNull()),
                ]);
            }

            $userBalanceChange = $featureListingByPackageService->makeFeaturedByBalance($listing, $package);
            $userBalanceChange->setDescription($trans->trans('trans.Activate package: %packageName%, for listing: %listingTitle%', [
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
