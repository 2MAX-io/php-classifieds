<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use App\Helper\Search;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminListingSearchService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->paginationService = $paginationService;
    }

    /**
     * @return Listing[]
     */
    public function getList(int $page): AdminListingListDto
    {
        $request = $this->requestStack->getMasterRequest();
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        if (!empty($_GET['query'])) {
            $qb->andWhere('MATCH (listing.searchText, listing.email, listing.phone, listing.rejectionReason) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimizeMatch($_GET['query']));
        }

        if (!empty($request->get('adminActivated', false))) {
            $qb->andWhere($qb->expr()->eq('listing.adminActivated', ':adminActivated'));
            $qb->setParameter(':adminActivated', $request->get('adminActivated'));
        }

        if (!empty($request->get('adminRejected', false))) {
            $qb->andWhere($qb->expr()->eq('listing.adminRejected', ':adminRejected'));
            $qb->setParameter(':adminRejected', $request->get('adminRejected'));
        }

        if (!empty($request->get('adminRemoved', false))) {
            $qb->andWhere($qb->expr()->eq('listing.adminRemoved', ':adminRemoved'));
            $qb->setParameter(':adminRemoved', $request->get('adminRemoved'));
        }

        if (!empty($request->get('userDeactivated', false))) {
            $qb->andWhere($qb->expr()->eq('listing.userDeactivated', ':userDeactivated'));
            $qb->setParameter(':userDeactivated', $request->get('userDeactivated'));
        }

        if (!empty($request->get('userRemoved', false))) {
            $qb->andWhere($qb->expr()->eq('listing.userRemoved', ':userRemoved'));
            $qb->setParameter(':userRemoved', $request->get('userRemoved'));
        }

        if (!empty($request->get('featured', false))) {
            $qb->andWhere($qb->expr()->eq('listing.featured', ':featured'));
            $qb->setParameter(':featured', $request->get('featured'));
        }

        if (!empty($request->get('user', false))) {
            $qb->join('listing.user', 'user');

            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('user.email', ':user'),
                    $qb->expr()->like('user.username', ':user'),
                    $qb->expr()->eq('user.id', ':user')
                )
            );
            $qb->setParameter(':user', Search::optimizeLike($request->get('user')));
        }

        $qb->orderBy('listing.id', 'DESC');

        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
    }
}
