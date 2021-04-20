<?php

declare(strict_types=1);

namespace App\Service\Listing\Package;

use App\Entity\Listing;
use App\Entity\Package;
use App\Helper\DateHelper;
use App\Service\Listing\Featured\FeatureListingService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class ApplyPackageToListingService
{
    /**
     * @var FeatureListingService
     */
    private $featureListingService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(FeatureListingService $featureListingService, EntityManagerInterface $em)
    {
        $this->featureListingService = $featureListingService;
        $this->em = $em;
    }

    public function applyPackageToListing(Listing $listing, ?Package $package, bool $paid = false): void
    {
        if (null === $package || ($package->isPaidPackage() && !$paid)) {
            if (!$listing->getExpirationDate()) {
                $listing->setExpirationDate(DateHelper::carbonNow());
            }

            return;
        }

        $this->applyExpirationDateFromPackage($listing, $package);
        $this->applyPullUpFromPackage($listing, $package);
        $listing->setFeaturedPriority($package->getListingFeaturedPriority());
        $listing->setUserDeactivated(false);

        $featuredTimeSeconds = $package->calculateSecondsFeaturedExpire();
        if ($featuredTimeSeconds > 0) {
            $this->featureListingService->makeFeatured($listing, $featuredTimeSeconds);
        }

        $this->em->persist($listing);
    }

    private function applyExpirationDateFromPackage(Listing $listing, Package $package): void
    {
        $baseExpirationDate = DateHelper::carbonNow();
        $isActive = DateHelper::carbonNow() < $listing->getExpirationDate();
        if ($isActive && $package->isPaidPackage()) {
            // when paid add valid until to current
            $baseExpirationDate = Carbon::instance($listing->getExpirationDate());
        }
        $newExpirationDate = $baseExpirationDate->addDays($package->getDaysListingExpire());
        if ($newExpirationDate < $listing->getExpirationDate()) {
            return;
        }
        $newExpirationDate->setTime(23, 59, 59);
        $listing->setExpirationDate($newExpirationDate);
    }

    private function applyPullUpFromPackage(Listing $listing, Package $package): void
    {
        if ($package->getPullUpOlderThanDays()
            && (DateHelper::olderThanDays($package->getPullUpOlderThanDays(), $listing->getOrderByDate()))
        ) {
            $listing->setOrderByDate(DateHelper::create());
        }
    }
}
