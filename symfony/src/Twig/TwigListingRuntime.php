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

    public function getListingStatusClass(Listing $listing): string
    {
        $map = [
            Listing::STATUS_ACTIVE => 'listing-status-active',
            Listing::STATUS_ACTIVE_FEATURED => 'listing-status-active-featured',
            Listing::STATUS_PENDING => 'listing-status-pending',
            Listing::STATUS_REJECTED => 'listing-status-rejected',
            Listing::STATUS_DEACTIVATED => 'listing-status-deactivated',
            Listing::STATUS_EXPIRED => 'listing-status-expired',
            Listing::STATUS_USER_REMOVED => 'listing-status-user-removed',
            Listing::STATUS_ADMIN_REMOVED => 'listing-status-admin-removed',
        ];

        $status = $listing->getStatus();

        return $map[$status] ?? 'listing-status-not-found';
    }
}
