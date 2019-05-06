<?php

declare(strict_types=1);

namespace App\Service\Admin\Other;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Entity\User;
use App\Service\Admin\Listing\ListingActivateListService;
use App\Service\Listing\ListingPublicDisplayService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class AdminStatsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingActivateListService
     */
    private $listingActivateListService;

    /**
     * @var ListingPublicDisplayService
     */
    private $publicDisplayService;

    public function __construct(
        EntityManagerInterface $em,
        ListingActivateListService $listingActivateListService,
        ListingPublicDisplayService $publicDisplayService
    ) {
        $this->em = $em;
        $this->listingActivateListService = $listingActivateListService;
        $this->publicDisplayService = $publicDisplayService;
    }

    public function getToActivateCount(): int
    {
        $qb = $this->listingActivateListService->getQueryBuilder();
        $qb->select($qb->expr()->count('listing.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAddedLastHours(int $hours): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->gte('listing.firstCreatedDate', ':listingsFrom'));
        $qb->setParameter('listingsFrom', Carbon::now()->subHours($hours)->setTime(1, 0, 0));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getActiveListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getAllListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getUserCount(): int
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
        $qb->select($qb->expr()->count('user.id'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getFeaturedListingsCount(): int
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->select($qb->expr()->count('listing.id'));
        $qb->andWhere($qb->expr()->eq('listing.featured', 1));
        $this->publicDisplayService->applyPublicDisplayConditions($qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getListingViewsCount(): int
    {
        $qb = $this->em->getRepository(ListingView::class)->createQueryBuilder('listingView');
        $qb->select('SUM(listingView.viewCount)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}