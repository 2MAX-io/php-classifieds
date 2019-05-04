<?php

declare(strict_types=1);

namespace App\Service\Admin\ListingAction;

use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;

class ListingActionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function activate(array $listingIds)
    {
        $listings = $this->em->getRepository(Listing::class)->getListingsFromIds($listingIds);

        $nowDatetime = new \DateTime();
        foreach ($listings as $listing) {
            $listing->setAdminActivated(true);
            $listing->setAdminRejected(false);
            $listing->setAdminLastActivationDate($nowDatetime);
            $this->em->persist($listing);
        }
    }

    public function reject(array $listingIds)
    {
        $listings = $this->em->getRepository(Listing::class)->getListingsFromIds($listingIds);

        foreach ($listings as $listing) {
            $listing->setAdminRejected(true);
            $listing->setRejectionReason('');
            $this->em->persist($listing);
        }
    }
}
