<?php

declare(strict_types=1);

namespace App\Service\Listing;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

class ListingPublicDisplayService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        RequestStack $requestStack
    ) {
        $this->currentUserService = $currentUserService;
        $this->settingsService = $settingsService;
        $this->requestStack = $requestStack;
    }

    public function applyPublicDisplayConditions(QueryBuilder $qb): void
    {
        $qb->andWhere($qb->expr()->gte('listing.validUntilDate', ':todayDayStart'));
        $qb->setParameter(':todayDayStart', \date('Y-m-d 00:00:00'));

        $qb->andWhere('listing.userDeactivated = 0');
        $qb->andWhere('listing.userRemoved = 0');
        $qb->andWhere('listing.adminRejected = 0');

        if ($this->settingsService->getSettingsDto()->getRequireListingAdminActivation()) {
            $qb->andWhere('listing.adminActivated = 1');
        }

        $qb->andWhere('listing.adminRemoved = 0');
    }

    public function canDisplay(Listing $listing, bool $showPreviewForOwner = false): bool
    {
        $showPreviewForOwner = $this->requestStack->getMasterRequest()->query->get('showPreviewForOwner', $showPreviewForOwner);
        if ($showPreviewForOwner && $this->currentUserService->isCurrentUser($listing->getUser())) {
            return true;
        }

        if ($showPreviewForOwner && $this->currentUserService->lowSecurityCheckIsAdminInPublic()) {
            return true;
        }

        /**
         * no need to check if:
         * - userDeactivated
         * - userRemoved
         *
         * when this happen listing is displayed as expired
         */
        return ($listing->getAdminActivated() || !$this->settingsService->getSettingsDto()->getRequireListingAdminActivation())
            && !$listing->getAdminRemoved()
            && !$listing->getAdminRejected()
        ;
    }
}
