<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\CustomField;
use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class ListingListService
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

        return $qb->getQuery()->getResult();
    }

    /**
     * @return CustomField[]
     */
    public function getCustomFields(): array
    {
        $qb = $this->em->getRepository(CustomField::class)->createQueryBuilder('custom_field');
        $qb->leftJoin('custom_field.customFieldOptions', 'custom_field_options');

        return $qb->getQuery()->getResult();
    }


}
