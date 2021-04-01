<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary;

use App\Entity\Listing;
use App\Helper\DateHelper;
use App\Service\Listing\ListingPublicDisplayService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class RecentListingsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingPublicDisplayService
     */
    private $listingPublicDisplayService;

    public function __construct(EntityManagerInterface $em, ListingPublicDisplayService $listingPublicDisplayService)
    {
        $this->em = $em;
        $this->listingPublicDisplayService = $listingPublicDisplayService;
    }

    /**
     * @return Listing[]
     */
    public function getLatestListings(int $count): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qb->orderBy('listing.id', Criteria::DESC);
        $qb->setMaxResults($count);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Listing[]
     */
    public function getListingsForRss(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);
        $qb->andWhere($qb->expr()->gt('listing.firstCreatedDate', ':listingsAfterDatetime'));
        $qb->setParameter(':listingsAfterDatetime', DateHelper::carbonNow()->subHours(48));
        $qb->orderBy('listing.id', Criteria::DESC);
        $results = $qb->getQuery()->getResult();

        if (0 === \count($results)) {
            $qb = $this->em->createQueryBuilder();
            $qb->select('listing');
            $qb->from(Listing::class, 'listing');
            $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);
            $qb->orderBy('listing.id', Criteria::DESC);
            $qb->setMaxResults(50);

            return $qb->getQuery()->getResult();
        }

        return $results;
    }

    /**
     * @return Listing[]
     */
    public function getRecommendedListings(int $maxResultsCount): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->andWhere($qb->expr()->eq('listing.featured', 1));
        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qbCount = clone $qb;
        $qbCount->select($qbCount->expr()->countDistinct('listing.id'));
        $count = (int) $qbCount->getQuery()->getSingleScalarResult();

        $maxResultToGetFromDb = $maxResultsCount * 5;
        $qb->setFirstResult(\random_int(0, \max($count - $maxResultToGetFromDb, 0)));
        $qb->setMaxResults($maxResultToGetFromDb);

        $results = $qb->getQuery()->getResult();
        \shuffle($results);

        return \array_slice($results, 0, $maxResultsCount);
    }
}
