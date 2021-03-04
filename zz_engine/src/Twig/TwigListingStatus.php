<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Service\Setting\EnvironmentService;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigListingStatus implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(TranslatorInterface $translator, EnvironmentService $environmentService, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->environmentService = $environmentService;
        $this->logger = $logger;
    }

    public function getListingStatus(Listing $listing): string
    {
        $status = $listing->getStatus();
        $twigDateFormatShort = $this->environmentService->getDateFormatShort();

        if ($status === $listing::STATUS_ACTIVE_FEATURED) {
            $featuredUntilDate = $this->translator->trans('trans.none');
            if ($listing->getFeaturedUntilDate() instanceof \DateTimeInterface) {
                $featuredUntilDate = $listing->getFeaturedUntilDate()->format($twigDateFormatShort);
            }

            return $this->translator->trans(
                "trans.listing.status.{$status}",
                [
                    '%featuredUntilDate%' => $featuredUntilDate,
                ]
            );
        }

        if ($status === $listing::STATUS_ACTIVE) {
            $activeUntilDate = $this->translator->trans('trans.none');
            if ($listing->getValidUntilDate() instanceof \DateTimeInterface) {
                $activeUntilDate = $listing->getValidUntilDate()->format($twigDateFormatShort);
            }

            return $this->translator->trans(
                "trans.listing.status.{$status}",
                [
                    '%activeUntilDate%' => $activeUntilDate,
                ]
            );
        }

        if ($status === $listing::STATUS_REJECTED && $listing->getRejectionReason()) {
            return $this->translator->trans(
                "trans.listing.status.{$status}.withReason",
                [
                    '%rejectionReason%' => $listing->getRejectionReason(),
                ]
            );
        }

        return $this->translator->trans("trans.listing.status.{$status}");
    }

    /**
     * CSS class for listing status
     */
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
        if (!isset($map[$status])) {
            $this->logger->error('listing status CSS class not found for: {status}', [
                'status' => $listing->getStatus(),
            ]);

            return 'listing-status-not-found';
        }

        return $map[$status];
    }
}
