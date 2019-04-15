<?php

declare(strict_types=1);

namespace App\Service\Listing\ShowSingle;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Service\Listing\ListingPublicDisplayService;
use Doctrine\ORM\EntityManagerInterface;

class ListingShowSingleService
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

    public function getSingle(int $listingId): ?ListingShowDto
    {
        $listingViewQuery = $this->em->getRepository(ListingView::class)->createQueryBuilder('listingView');
        $listingViewQuery->select('SUM(listingView.viewCount)');
        $listingViewQuery->andWhere($listingViewQuery->expr()->eq('listingView.listing', 'listing.id'));
        $viewsCountQuery = $listingViewQuery->getQuery()->getDQL();
        $viewsCountQuery = "($viewsCountQuery) AS viewsCount";

        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('customField');
        $qb->addSelect('listingCustomFieldValue');
        $qb->addSelect('customFieldOption');
        $qb->addSelect('listingFile');
        $qb->addSelect($viewsCountQuery);
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');
        $qb->leftJoin('listingCustomFieldValue.customFieldOption', 'customFieldOption');
        $qb->leftJoin('listingCustomFieldValue.customField', 'customField');
        $qb->leftJoin('listing.listingFiles', 'listingFile');
        $qb->andWhere($qb->expr()->eq('listing.id', ':listingId'));
        $qb->setParameter(':listingId', $listingId);

        return ListingShowDto::fromDoctrineResult($qb->getQuery()->getOneOrNullResult());
    }

    public function saveView(Listing $listing): void
    {
        $query = $this->em->getConnection()->prepare('
            INSERT INTO listing_view 
            SET listing_id=:listingId,
                view_count=1,
                datetime=:datetime
        ');
        $query->bindValue(':listingId', $listing->getId());
        $query->bindValue(':datetime', date('Y-m-d H:i:s'));
        $query->execute();
    }
}
