<?php

declare(strict_types=1);

namespace App\Service\Listing;

use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsDto;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

class ListingPublicDisplayService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        CurrentUserService $currentUserService,
        SettingsDto $settingsDto,
        RequestStack $requestStack
    ) {
        $this->currentUserService = $currentUserService;
        $this->settingsDto = $settingsDto;
        $this->requestStack = $requestStack;
    }

    public function applyPublicDisplayConditions(QueryBuilder $qb): void
    {
        $qb->andWhere($qb->expr()->gte('listing.expirationDate', ':startOfToday'));
        $qb->setParameter(':startOfToday', DateHelper::date('Y-m-d 00:00:00'));

        $qb->andWhere($qb->expr()->eq('listing.userDeactivated', 0));
        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminRejected', 0));

        if ($this->settingsDto->getRequireListingAdminActivation()) {
            $qb->andWhere($qb->expr()->eq('listing.adminActivated', 1));
        }

        $qb->andWhere($qb->expr()->eq('listing.adminRemoved', 0));
    }

    public function applyHiddenConditions(QueryBuilder $qb): void
    {
        $orx = $qb->expr()->orX();
        $orx->add($qb->expr()->lt('listing.expirationDate', ':startOfToday'));
        $qb->setParameter(':startOfToday', DateHelper::date('Y-m-d 00:00:00'));

        $orx->add($qb->expr()->eq('listing.userDeactivated', 1));
        $orx->add($qb->expr()->eq('listing.userRemoved', 1));
        $orx->add($qb->expr()->eq('listing.adminRejected', 1));

        if ($this->settingsDto->getRequireListingAdminActivation()) {
            $qb->andWhere($qb->expr()->eq('listing.adminActivated', 0));
        }

        $orx->add($qb->expr()->eq('listing.adminRemoved', 1));
        $qb->andWhere($orx);
    }

    public function canDisplay(Listing $listing, bool $showListingPreviewForOwner = false): bool
    {
        $request = $this->requestStack->getMasterRequest();
        if ($request) {
            $showListingPreviewForOwner = $request->get(ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER, $showListingPreviewForOwner);
            if ($showListingPreviewForOwner) {
                if ($this->currentUserService->isCurrentUser($listing->getUser())) {
                    return true;
                }

                if ($this->currentUserService->isAdminInPublic()) {
                    return true;
                }
            }
        }

        return ($listing->getAdminActivated() || !$this->settingsDto->getRequireListingAdminActivation())
            && !$listing->getAdminRemoved()
            && !$listing->getAdminRejected()
        ;
    }
}
