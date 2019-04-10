<?php

declare(strict_types=1);

namespace App\Service\Listing\ShowSingle;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class ListingShowSingleService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSingle(int $listingId): ?Listing
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->addSelect('listingCustomFieldValue');
        $qb->addSelect('customField');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');
        $qb->leftJoin('listingCustomFieldValue.customField', 'customField');
        $qb->leftJoin('listing.listingFiles', 'listingFile');
        $qb->andWhere($qb->expr()->eq('listing.id', ':listingId'));
        $qb->setParameter(':listingId', $listingId);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
