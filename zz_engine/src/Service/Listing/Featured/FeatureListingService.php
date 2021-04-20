<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Listing;
use App\Helper\DateHelper;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class FeatureListingService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function makeFeatured(Listing $listing, int $featuredTimeSeconds): void
    {
        $featuredUntilDate = $listing->getFeaturedUntilDate();
        $isFeatured = $featuredUntilDate > DateHelper::carbonNow();
        $baseFeaturedUntilDate = DateHelper::carbonNow();
        if ($featuredUntilDate && $isFeatured) {
            $baseFeaturedUntilDate = $featuredUntilDate;
        }

        $newFeaturedUntilDate = Carbon::instance($baseFeaturedUntilDate)->addSeconds($featuredTimeSeconds);
        $setFeaturedDateToTheEndOfTheDay = $featuredTimeSeconds > 20 * 3600
            && $newFeaturedUntilDate > DateHelper::carbonNow()->addHours(16);
        if ($setFeaturedDateToTheEndOfTheDay) {
            $newFeaturedUntilDate->setTime(23, 59, 59);
        }

        $listing->setFeatured(true);
        $listing->setFeaturedUntilDate($newFeaturedUntilDate);
        $listing->setOrderByDate(DateHelper::create());

        $this->preventListingValidDateLessThanFeatured($listing);

        $this->em->persist($listing);
    }

    private function preventListingValidDateLessThanFeatured(Listing $listing): void
    {
        if ($listing->getExpirationDate() < $listing->getFeaturedUntilDate()) {
            $listing->setExpirationDate($listing->getFeaturedUntilDate());
        }
    }
}
