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
    public function getLatestListingList(int $count): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->orderBy('listing.firstCreatedDate', 'DESC');

        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $qb->setMaxResults($count);

        return $qb->getQuery()->getResult();
    }
}
