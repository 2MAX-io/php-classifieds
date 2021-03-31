<?php

declare(strict_types=1);

namespace App\Service\Listing\ShowSingle;

use App\Entity\Listing;
use App\Entity\ListingView;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class ListingShowSingleService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService, EntityManagerInterface $em)
    {
        $this->currentUserService = $currentUserService;
        $this->em = $em;
    }

    public function getSingle(int $listingId): ?ListingShowDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->addSelect('customField');
        $qb->addSelect('listingCustomFieldValue');
        $qb->addSelect('customFieldOption');
        $qb->addSelect('listingFile');
        $qb->addSelect('category');
        $qb->addSelect($this->getListingViewsQueryForSelect());
        $qb->join('listing.category', 'category');
        $qb->leftJoin('category.customFieldForCategoryList', 'customFieldForCategoryList');
        $qb->leftJoin(
            'listing.listingCustomFieldValues',
            'listingCustomFieldValue',
            Join::WITH,
            (string) $qb->expr()->eq(
                'customFieldForCategoryList.customField',
                'listingCustomFieldValue.customField',
            )
        );
        $qb->leftJoin('listingCustomFieldValue.customFieldOption', 'customFieldOption');
        $qb->leftJoin('listingCustomFieldValue.customField', 'customField');
        $qb->leftJoin('listing.listingFiles', 'listingFile');
        $qb->leftJoin(
            'customField.customFieldForCategories',
            'customFieldForCategory',
            Join::WITH,
            (string) $qb->expr()->andX(
                $qb->expr()->eq('customFieldForCategory.category', 'listing.category'),
            )
        );

        $currentUser = $this->currentUserService->getUserOrNull();
        $qb->addSelect('userObservedListing');
        $qb->leftJoin('listing.userObservedListings',
            'userObservedListing',
            Join::WITH,
            (string) $qb->expr()->eq('userObservedListing.user', ':currentUserId'),
        );
        $qb->setParameter(':currentUserId', $currentUser ? $currentUser->getId() : 0);

        $qb->andWhere($qb->expr()->eq('listing.id', ':listingId'));
        $qb->setParameter(':listingId', $listingId);

        $qb->addOrderBy('customFieldForCategoryList.sort', Criteria::ASC);
        $qb->addOrderBy('customFieldOption.sort', Criteria::ASC);
        $qb->addOrderBy('customField.sort', Criteria::ASC);

        return ListingShowDto::fromDoctrineResult($qb->getQuery()->getOneOrNullResult());
    }

    public function saveView(Listing $listing): void
    {
        $query = $this->em->getConnection()->prepare('
            INSERT INTO listing_view 
            SET 
                listing_id = :listingId,
                view_count = 1,
                datetime = :datetime
        ');
        $query->bindValue(':listingId', $listing->getId());
        $query->bindValue(':datetime', DateHelper::date(DateHelper::MYSQL_FORMAT));
        $query->execute();
    }

    private function getListingViewsQueryForSelect(): string
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listingView');
        $qb->from(ListingView::class, 'listingView');
        $qb->select('SUM(listingView.viewCount)');
        $qb->andWhere($qb->expr()->eq('listingView.listing', 'listing.id'));
        $dql = $qb->getQuery()->getDQL();

        return "({$dql}) AS viewsCount";
    }
}
