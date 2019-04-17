<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\System\EnvironmentService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigListingRuntime implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    public function __construct(TranslatorInterface $translator, EnvironmentService $environmentService)
    {
        $this->translator = $translator;
        $this->environmentService = $environmentService;
    }

    public function getListingStatus(Listing $listing): string
    {
        $status = $listing->getStatus();

        if ($status === $listing::STATUS_ACTIVE_FEATURED) {
            return $this->translator->trans(
                "trans.listing.status.$status",
                [
                    '%featuredUntilDate%' => $listing->getValidUntilDate()->format(
                        $this->environmentService->getTwigDateFormatShort()
                    ),
                ]
            );
        }

        if ($status === $listing::STATUS_ACTIVE) {
            return $this->translator->trans(
                "trans.listing.status.$status",
                [
                    '%activeUntilDate%' => $listing->getValidUntilDate()->format(
                        $this->environmentService->getTwigDateFormatShort()
                    ),
                ]
            );
        }

        if ($status === $listing::STATUS_REJECTED && $listing->getRejectionReason()) {
            return $this->translator->trans(
                "trans.listing.status.$status.withReason",
                [
                    '%rejectionReason%' => $listing->getRejectionReason(),
                ]
            );
        }

        return $this->translator->trans("trans.listing.status.$status");
    }
}
