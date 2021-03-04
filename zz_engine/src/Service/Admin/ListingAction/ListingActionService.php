<?php

declare(strict_types=1);

namespace App\Service\Admin\ListingAction;

use App\Helper\DateHelper;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;

class ListingActionService
{
    /**
     * @var ListingRepository
     */
    private $listingRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ListingRepository $listingRepository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->listingRepository = $listingRepository;
    }

    /**
     * @param int[] $listingIds
     */
    public function activate(array $listingIds): void
    {
        $listings = $this->listingRepository->getFromIds($listingIds);
        $nowDatetime = DateHelper::create();
        foreach ($listings as $listing) {
            $listing->setAdminActivated(true);
            $listing->setAdminRejected(false);
            $listing->setAdminLastActivationDate($nowDatetime);
            $this->em->persist($listing);
        }
    }

    /**
     * @param int[] $listingIds
     */
    public function reject(array $listingIds): void
    {
        $listings = $this->listingRepository->getFromIds($listingIds);
        foreach ($listings as $listing) {
            $listing->setAdminRejected(true);
            $listing->setRejectionReason(null);
            $this->em->persist($listing);
        }
    }
}
