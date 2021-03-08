<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use App\Helper\RandomHelper;
use App\Service\Admin\Listing\Dto\AdminListingListDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ListingActivateService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(PaginationService $paginationService, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->paginationService = $paginationService;
    }

    public function getAwaitingActivationList(int $page): AdminListingListDto
    {
        $qb = $this->getAwaitingActivationQueryBuilder();

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto();
        $adminListingListDto->setResults($pager->getCurrentPageResults());
        $adminListingListDto->setPager($pager);

        return $adminListingListDto;
    }

    public function getNextListingToActivate(): ?Listing
    {
        $qb = $this->getAwaitingActivationQueryBuilder();
        $qbCount = clone $qb;
        $qbCount->select($qbCount->expr()->countDistinct('listing.id'));
        $count = (int) $qbCount->getQuery()->getSingleScalarResult();

        $maxResultToGetFromDb = 36;
        $qb->setFirstResult(\random_int(0, \max($count - $maxResultToGetFromDb, 0)));
        $qb->setMaxResults($maxResultToGetFromDb);

        /** @var Listing|null $listing */
        $listing = RandomHelper::fromArray($qb->getQuery()->getResult());

        return $listing;
    }

    public function getAwaitingActivationQueryBuilder(): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');

        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.userDeactivated', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminRejected', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminActivated', 0));

        $qb->addOrderBy('listing.featured', Criteria::DESC);
        $qb->addOrderBy('listing.lastEditDate', Criteria::ASC);
        $qb->addOrderBy('listing.firstCreatedDate', Criteria::ASC);

        return $qb;
    }
}
