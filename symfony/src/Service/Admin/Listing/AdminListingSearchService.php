<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use App\Helper\Search;
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

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
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
            $qb->setParameter(':query', Search::optimize($_GET['query']));
        }

        if (!empty($request->get('adminConfirmed', false))) {
            $qb->andWhere($qb->expr()->eq('listing.adminConfirmed', ':adminConfirmed'));
            $qb->setParameter(':adminConfirmed', $request->get('adminConfirmed'));
        }

        $qb->orderBy('listing.id', 'DESC');

        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
    }
}
