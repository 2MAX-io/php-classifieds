<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ListingActivateListService
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
    public function getToActivateListingList(int $page): AdminListingListDto
    {
        $qb = $this->getQueryBuilder();

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
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
