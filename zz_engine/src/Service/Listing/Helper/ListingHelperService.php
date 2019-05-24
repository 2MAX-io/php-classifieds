<?php

declare(strict_types=1);

namespace App\Service\Listing\Helper;

use App\Entity\Listing;
use App\Service\Listing\ListingPublicDisplayService;
use Doctrine\ORM\EntityManagerInterface;

class ListingHelperService
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
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('listingFile');
        $qb->leftJoin('listing.listingFiles', 'listingFile');
        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qb->orderBy('listing.id', 'DESC');
        $qb->groupBy('listing.id');
        $qb->setMaxResults($count);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Listing[]
     */
    public function getRecommendedListings(int $maxResultsCount): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('listingFile');
        $qb->leftJoin('listing.listingFiles', 'listingFile');
        $qb->andWhere($qb->expr()->eq('listing.featured', 1));
        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qbCount = clone $qb;
        $qbCount->select($qbCount->expr()->countDistinct('listing.id'));
        $count = (int) $qbCount->getQuery()->getSingleScalarResult();

        $maxResultToGetFromDb = $maxResultsCount * 5;
        $qb->setFirstResult(random_int(0, max($count-($maxResultToGetFromDb), 0)));
        $qb->setMaxResults($maxResultToGetFromDb);
        $qb->groupBy('listing.id');

        $results = $qb->getQuery()->getResult();
        shuffle($results);
        $results = array_slice($results, 0, $maxResultsCount);

        return $results;
    }
}
