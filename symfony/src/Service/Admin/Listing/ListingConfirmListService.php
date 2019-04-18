<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ListingConfirmListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Listing[]
     */
    public function getToConfirmListingList(int $page): AdminListingListDto
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        $qb->andWhere($qb->expr()->eq('listing.adminConfirmed', 0));
        $qb->addOrderBy('listing.lastEditDate', 'ASC');
        $qb->addOrderBy('listing.firstCreatedDate', 'ASC');

        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        $adminListingListDto = new AdminListingListDto($pager->getCurrentPageResults(), $pager);

        return $adminListingListDto;
    }
}
