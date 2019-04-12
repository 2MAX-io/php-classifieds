<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class ListingSearchService
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
    public function getListings(): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        if (!empty($_GET['query'])) {
            $qb->andWhere('MATCH (listing.searchText, listing.email, listing.phone, listing.rejectionReason) AGAINST (:query BOOLEAN) > 0');
            $qb->setParameter(':query', rtrim($_GET['query'], '*') .'*');
        }

        return $qb->getQuery()->getResult();
    }
}
