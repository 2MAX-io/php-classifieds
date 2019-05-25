<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\FeaturedPackage;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\UserBalanceChange;
use App\Security\CurrentUserService;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Money\UserBalanceService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        EntityManagerInterface $em,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService,
        ValidUntilSetService $validUntilSetService,
        TranslatorInterface $trans
    ) {
        $this->em = $em;
        $this->userBalanceService = $userBalanceService;
        $this->currentUserService = $currentUserService;
        $this->validUntilSetService = $validUntilSetService;
        $this->trans = $trans;
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

    public function makeFeaturedByBalance(Listing $listing, FeaturedPackage $featuredPackage, Payment $payment = null): ?UserBalanceChange
    {
        $this->em->beginTransaction();

        if ($listing->getUser() !== $this->currentUserService->getUser()) {
            throw new \Exception('listing of different user');
        }

        try {
            $cost = $featuredPackage->getPrice();
            if (!$this->userBalanceService->hasAmount($cost, $listing->getUser())) {
                return null;
            }

            $this->makeFeatured($listing, $featuredPackage->getDaysFeaturedExpire() * 3600 * 24);
            $userBalanceChange = $this->userBalanceService->removeBalance($cost, $listing->getUser(), $payment);
            $this->validUntilSetService->addValidityDaysWithoutRestrictions(
                $listing,
                $featuredPackage->getDaysListingExpire()
            );
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }

        $this->em->commit();

        return $userBalanceChange;
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

    public function hasAmount(Listing $listing, FeaturedPackage $featuredPackage): bool
    {
        $cost = $featuredPackage->getPrice();

        return $this->userBalanceService->hasAmount($cost, $listing->getUser());
    }
}