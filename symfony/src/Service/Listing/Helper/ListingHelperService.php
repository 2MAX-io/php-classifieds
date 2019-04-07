<?php

declare(strict_types=1);

namespace App\Service\Listing\Helper;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class ListingHelperService
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
    public function getLatestListingList(): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        return $qb->getQuery()->getResult();
    }
}
