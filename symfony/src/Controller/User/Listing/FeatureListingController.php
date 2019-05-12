<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Security\CurrentUserService;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Listing\Featured\FeaturedPackageService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
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

        return $this->render('user/listing/featured_extend.html.twig', [
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
        FeaturedListingService $featuredListingService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('featureAsDemo'.$listing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $featuredListingService->makeFeaturedAsDemo($listing);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $listing->getId(), 'demoStarted' => 1]
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route(
     *     "/user/feature/make-featured/package/{featuredPackage}/listing/{id}",
     *     name="app_user_feature_listing_by_balance",
     *     methods={"PATCH"},
     * )
     */
    public function makeFeatured(
        Listing $listing,
        FeaturedPackage $featuredPackage,
        FeaturedListingService $featuredListingService,
        PaymentService $paymentService,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService,
        TranslatorInterface $trans,
        Request $request
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if ($this->isCsrfTokenValid('feature'.$listing->getId(), $request->request->get('_token'))) {
            $previousBalance = $userBalanceService->getCurrentBalance($currentUserService->getUser());
            $em = $this->getDoctrine()->getManager();

            if ($featuredListingService->hasAmount($listing, $featuredPackage)) {
                $userBalanceChange = $featuredListingService->makeFeaturedByBalance($listing, $featuredPackage);
                $userBalanceChange->setDescription($trans->trans('trans.Featuring listing using balance, listing: id:%listingId% - %listingTitle%, using featured package: %featuredPackageName%, price: %price%, previous balance: %previousBalance%, current balance: %currentBalance%', [
                    '%listingId%' => $listing->getId(),
                    '%listingTitle%' => $listing->getTitle(),
                    '%featuredPackageName%' => $featuredPackage->getName(),
                    '%price%' => $featuredPackage->getPrice() / 100,
                    '%previousBalance%' => $previousBalance / 100,
                    '%currentBalance%' => $userBalanceChange->getBalanceFinal() / 100,
                ]));
                $em->flush();
            } else {
                $paymentDto = $paymentService->createPaymentForFeaturedPackage($listing, $featuredPackage);
                $em->flush();

                return $this->redirect($paymentDto->getPaymentExecuteUrl());
            }

            return $this->redirectToRoute(
                'app_user_feature_listing',
                ['id' => $listing->getId()]
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
