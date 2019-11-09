<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\FeaturedPackage;
use App\Entity\FeaturedPackageForCategory;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\UserBalanceChange;
use App\Security\CurrentUserService;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Money\UserBalanceService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedListingService
{
    /**
     * @var EntityManagerInterface|EntityManager
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

    public function __construct(
        EntityManagerInterface $em,
        UserBalanceService $userBalanceService,
        CurrentUserService $currentUserService,
        ValidUntilSetService $validUntilSetService
    ) {
        $this->em = $em;
        $this->userBalanceService = $userBalanceService;
        $this->currentUserService = $currentUserService;
        $this->validUntilSetService = $validUntilSetService;
    }

    public function makeFeatured(Listing $listing, int $featuredTimeSeconds): void
    {
        $featuredUntilDate = $listing->getFeaturedUntilDate();
        $baseDatetimeToAddFeaturedInterval = Carbon::now();
        if (null !== $featuredUntilDate && $featuredUntilDate > Carbon::now()) {
            $baseDatetimeToAddFeaturedInterval = $featuredUntilDate;
        }

        $newFeaturedUntilDate = Carbon::instance($baseDatetimeToAddFeaturedInterval)->addSeconds($featuredTimeSeconds);
        if ($newFeaturedUntilDate > Carbon::now()->addHour(16)) {
            $newFeaturedUntilDate->setTime(23, 59, 59);
        }

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

    public function makeFeaturedByBalance(Listing $listing, FeaturedPackage $featuredPackage, Payment $payment = null): UserBalanceChange
    {
        $this->em->beginTransaction();

        $paymentAndListingHasSameUser = $payment && $listing->getUser() === $payment->getUser();
        $listingOfCurrentUser = $listing->getUser() === $this->currentUserService->getUserOrNull();
        if (!$paymentAndListingHasSameUser && !$listingOfCurrentUser) {
            if ($payment && !$paymentAndListingHasSameUser) {
                throw new \RuntimeException('payment and listing does not have same user');
            }
            if (!$listingOfCurrentUser) {
                throw new \RuntimeException('not current user listing');
            }
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

    public function isPackageForListingCategory(Listing $listing, FeaturedPackage $featuredPackage): bool
    {
        if ($featuredPackage->getDefaultPackage() && !$this->hasNotDefaultPackage($listing)) {
            return true;
        }

        $qb = $this->em->getRepository(FeaturedPackageForCategory::class)->createQueryBuilder('featuredPackageForCategory');
        $qb->select($qb->expr()->count('featuredPackageForCategory.id'));
        $qb->join('featuredPackageForCategory.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.featuredPackage', ':featuredPackage'));
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->setParameter(':featuredPackage', $featuredPackage);
        $qb->setParameter(':category', $listing->getCategory());

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function hasNotDefaultPackage(Listing $listing): bool
    {
        $qb = $this->em->getRepository(FeaturedPackageForCategory::class)->createQueryBuilder('featuredPackageForCategory');
        $qb->select($qb->expr()->count('featuredPackageForCategory.id'));
        $qb->join('featuredPackageForCategory.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->setParameter(':category', $listing->getCategory());

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
