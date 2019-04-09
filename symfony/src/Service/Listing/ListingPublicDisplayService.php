<?php

declare(strict_types=1);

namespace App\Service\Listing;

use App\Entity\Listing;
use Doctrine\ORM\QueryBuilder;

class ListingPublicDisplayService
{
    public function applyPublicDisplayConditions(QueryBuilder $qb)
    {
        $qb->andWhere($qb->expr()->gte('listing.validUntilDate', ':validUntil'));
        $qb->setParameter(':validUntil', date('Y-m-d H:i:59'));

        $qb->andWhere('listing.adminConfirmed = 1');
        $qb->andWhere('listing.userRemoved = 0');
        $qb->andWhere('listing.userDeactivated = 0');
        $qb->andWhere('listing.adminRemoved = 0');
    }

    public function canPublicDisplay(Listing $listing): bool
    {
        return $listing->getAdminRemoved() === false;
    }
}
