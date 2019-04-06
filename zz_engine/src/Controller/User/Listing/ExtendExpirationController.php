<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Form\Listing\ExtendExpiration\ExtendExpirationDto;
use App\Form\Listing\ExtendExpiration\ExtendExpirationType;
use App\Security\CurrentUserService;
use App\Service\Listing\Package\ApplyPackageToListingService;
use App\Service\Money\UserBalanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ExtendExpirationController extends AbstractUserController
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, EntityManagerInterface $em)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->em = $em;
    }

    /**
     * @Route("/user/extend-listing/{id}", name="app_user_extend_expiration")
     */
    public function extendExpiration(
        Request $request,
        Listing $listing,
        ApplyPackageToListingService $applyPackageToListingService,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing);

        $extendExpirationDto = new ExtendExpirationDto();
        $form = $this->createForm(ExtendExpirationType::class, $extendExpirationDto, [
            'category' => $listing->getCategory(),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $applyPackageToListingService->applyPackageToListing(
                $listing,
                $extendExpirationDto->getPackage(),
            );

            $this->em->persist($listing);
            $this->em->flush();

            if ($extendExpirationDto->getPackageNotNull()->isPaidPackage()) {
                return $this->redirectToRoute('app_user_feature_listing_pay', [
                    'id' => $listing->getId(),
                    'package' => $extendExpirationDto->getPackageNotNull()->getId(),
                    '_token' => $this->csrfTokenManager->getToken('csrf_feature'.$listing->getId())->getValue(),
                ]);
            }

            return $this->redirectToRoute('app_user_extend_expiration', ['id' => $listing->getId()]);
        }

        return $this->render('user/listing/extend_expiration.html.twig', [
            'form' => $form->createView(),
            'listing' => $listing,
            'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUserOrNull()),
        ]);
    }
}
