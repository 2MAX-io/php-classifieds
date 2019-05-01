<?php

declare(strict_types=1);

namespace App\Service\Listing;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use Doctrine\ORM\QueryBuilder;

class ListingPublicDisplayService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    public function applyPublicDisplayConditions(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->gte('listing.validUntilDate', ':todayDayStart'));
        $qb->setParameter(':todayDayStart', date('Y-m-d 00:00:00'));

        $qb->andWhere('listing.adminActivated = 1');
        $qb->andWhere('listing.userRemoved = 0');
        $qb->andWhere('listing.userDeactivated = 0');
        $qb->andWhere('listing.adminRemoved = 0');
    }

    public function canDisplay(Listing $listing): bool
    {
        if ($this->currentUserService->isCurrentUser($listing->getUser())) {
            return true;
        }

        if ($this->currentUserService->lowSecurityCheckIsAdminInPublic()) {
            return true;
        }

        return $listing->getAdminActivated() && !$listing->getAdminRemoved() && !$listing->getAdminRejected();
    }
}
