<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

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
    public function getToConfirmListingList(): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        $qb->andWhere($qb->expr()->eq('listing.adminConfirmed', 0));
        $qb->addOrderBy('listing.lastEditDate', 'ASC');
        $qb->addOrderBy('listing.firstCreatedDate', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
