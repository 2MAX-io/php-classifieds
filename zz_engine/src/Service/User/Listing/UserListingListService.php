<?php

declare(strict_types=1);

namespace App\Service\User\Listing;

use App\Entity\Listing;
use App\Helper\SearchHelper;
use App\Security\CurrentUserService;
use App\Service\System\Pagination\PaginationService;
use App\Service\User\Listing\Dto\UserListingListDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class UserListingListService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        CurrentUserService $currentUserService,
        PaginationService $paginationService,
        EntityManagerInterface $em
    ) {
        $this->currentUserService = $currentUserService;
        $this->paginationService = $paginationService;
        $this->em = $em;
    }

    public function getList(int $page = 1, string $searchQuery = null): UserListingListDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->addSelect('category');
        $qb->addSelect('categoryParent');
        $qb->join('listing.category', 'category');
        $qb->join('category.parent', 'categoryParent');
        $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
        $qb->setParameter(':user', $this->currentUserService->getUserOrNull());

        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));

        if ($searchQuery) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', SearchHelper::optimizeMatch($searchQuery));
        }

        $qb->orderBy('listing.lastEditDate', Criteria::DESC);

        $adapter = new QueryAdapter($qb, false, null !== $qb->getDQLPart('having'));
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($page);

        return new UserListingListDto($pager->getCurrentPageResults(), $pager);
    }
}
