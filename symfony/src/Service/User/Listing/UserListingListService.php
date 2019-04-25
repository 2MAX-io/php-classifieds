<?php

declare(strict_types=1);

namespace App\Service\User\Listing;

use App\Entity\Listing;
use App\Helper\Search;
use App\Security\CurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

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

    public function __construct(EntityManagerInterface $em, CurrentUserService $currentUserService)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    /**
     * @return Listing[]
     */
    public function getList(int $page = 1): UserListingListDto
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('category');
        $qb->addSelect('categoryParent');
        $qb->join('listing.category', 'category');
        $qb->join('category.parent', 'categoryParent');
        $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
        $qb->setParameter(':user', $this->currentUserService->getUser());

        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));

        if (!empty($_GET['query'])) {
            $qb->andWhere('MATCH (listing.searchText) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', Search::optimize($_GET['query']));
        }

        $qb->orderBy('listing.lastEditDate', 'DESC');

        $adapter = new DoctrineORMAdapter($qb, false, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        $userListingListDto = new UserListingListDto($pager->getCurrentPageResults(), $pager);

        return $userListingListDto;
    }
}
