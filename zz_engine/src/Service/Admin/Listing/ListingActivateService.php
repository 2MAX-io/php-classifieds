<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use App\Helper\Arr;
use App\Service\System\Pagination\PaginationService;
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

    public function __construct(EntityManagerInterface $em, PaginationService $paginationService)
    {
        $this->em = $em;
        $this->paginationService = $paginationService;
    }

    /**
     * @return Listing[]
     */
    public function getAwaitingActivationList(int $page): AdminListingListDto
    {
        $qb = $this->getQueryBuilder();

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
    }

    public function getNextWaitingListing(): ?Listing
    {
        $qb = $this->getQueryBuilder();
        $qb->setMaxResults(50);

        return Arr::random($qb->getQuery()->getResult());
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.userDeactivated', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminRejected', 0));
        $qb->andWhere($qb->expr()->eq('listing.adminActivated', 0));

        $qb->addOrderBy('listing.featured', 'DESC');
        $qb->addOrderBy('listing.lastEditDate', 'ASC');
        $qb->addOrderBy('listing.firstCreatedDate', 'ASC');

        return $qb;
    }
}
