<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Listing;
use App\Service\Money\UserBalanceService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedListingService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserBalanceService
     */
    private $userBalanceService;

    public function __construct(EntityManagerInterface $em, UserBalanceService $userBalanceService)
    {
        $this->em = $em;
        $this->userBalanceService = $userBalanceService;
    }

    public function makeFeatured(Listing $listing, int $featuredTimeSeconds): void
    {
        $featuredUntilDate = $listing->getFeaturedUntilDate();

        $baseDatetimeToAddFeaturedInterval = Carbon::now();
        if (null !== $featuredUntilDate && $featuredUntilDate > Carbon::now()) {
            $baseDatetimeToAddFeaturedInterval = $featuredUntilDate;
        }

        $newFeaturedUntilDate = Carbon::instance($baseDatetimeToAddFeaturedInterval)->addSeconds($featuredTimeSeconds);

        $listing->setFeatured(true);
        $listing->setFeaturedUntilDate($newFeaturedUntilDate);
        $listing->setOrderByDate(new \DateTime());

        $this->preventListingValidDateLessThanFeatured($listing);

        $this->em->persist($listing);
    }

    public function makeFeaturedAsDemo(Listing $listing): void
    {
        if ($listing->getFeaturedUntilDate() === null) {
            $this->makeFeatured($listing, 1800);
        }
    }

    public function makeFeaturedByBalance(Listing $listing, int $featuredTimeSeconds): void
    {
        // todo: secure that listing belongs to user

        $cost = 1 * 100;
        if (!$this->userBalanceService->hasAmount($cost, $listing->getUser())) {
            return;
        }

        $this->makeFeatured($listing, $featuredTimeSeconds);
        $this->userBalanceService->removeBalance($cost, $listing->getUser());
    }

    /**
     * @param Listing $listing
     */
    public function preventListingValidDateLessThanFeatured(Listing $listing): void
    {
        if ($listing->getValidUntilDate() < $listing->getFeaturedUntilDate()) {
            $listing->setValidUntilDate($listing->getFeaturedUntilDate());
        }
    }
}
