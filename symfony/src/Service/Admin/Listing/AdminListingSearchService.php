<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class AdminListingSearchService
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
    public function getList(int $page): AdminListingListDto
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        if (!empty($_GET['query'])) {
            $qb->andWhere('MATCH (listing.searchText, listing.email, listing.phone, listing.rejectionReason) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', rtrim($_GET['query'], '*') .'*');
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
