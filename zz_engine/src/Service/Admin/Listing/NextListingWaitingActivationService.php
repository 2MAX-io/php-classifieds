<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Enum\ParamEnum;
use App\Service\System\Routing\RefererService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NextListingWaitingActivationService
{
    /**
     * @var RefererService
     */
    private $refererService;

    /**
     * @var ListingActivateService
     */
    private $listingActivateService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        ListingActivateService $listingActivateService,
        RefererService $refererService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->refererService = $refererService;
        $this->listingActivateService = $listingActivateService;
        $this->urlGenerator = $urlGenerator;
    }

    public function nextWaitingRedirectResponse(): Response
    {
        $newWaitingListing = $this->listingActivateService->getNextListingToActivate();
        if ($newWaitingListing) {
            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_listing_show',
                    [
                        'id' => $newWaitingListing->getId(),
                        'slug' => $newWaitingListing->getSlug(),
                        ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER => 1,
                    ],
                )
            );
        }

        return $this->refererService->redirectToRefererResponse();
    }
}
