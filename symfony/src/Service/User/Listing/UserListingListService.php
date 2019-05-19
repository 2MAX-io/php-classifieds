<?php

declare(strict_types=1);

namespace App\Service\User\Listing;

use App\Entity\Listing;
use App\Helper\Search;
use App\Security\CurrentUserService;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class UserListingListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        CurrentUserService $currentUserService,
        RequestStack $requestStack,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
        $this->paginationService = $paginationService;
        $this->requestStack = $requestStack;
    }

    /**
     * @return Listing[]
     */
    public function getList(int $page = 1): UserListingListDto
    {
        $request = $this->requestStack->getMasterRequest();

        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('category');
        $qb->addSelect('categoryParent');
        $qb->join('listing.category', 'category');
        $qb->join('category.parent', 'categoryParent');
        $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
        $qb->setParameter(':user', $this->currentUserService->getUser());

        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));

        if ($request->get('query', false)) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimizeMatch($request->get('query')));
        }

        $qb->orderBy('listing.lastEditDate', 'DESC');

        $adapter = new DoctrineORMAdapter($qb, false, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        $userListingListDto = new UserListingListDto($pager->getCurrentPageResults(), $pager);

        return $userListingListDto;
    }
}
