<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Category;
use App\Entity\FeaturedPackage;
use App\Entity\FeaturedPackageForCategory;
use App\Entity\Listing;
use App\Entity\Payment;
use App\Entity\UserBalanceChange;
use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Money\UserBalanceService;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedListingService
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var UserBalanceService
     */
    private $userBalanceService;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    public function __construct(
        UserBalanceService $userBalanceService,
        ValidUntilSetService $validUntilSetService,
        CurrentUserService $currentUserService,
        EntityManagerInterface $em
    ) {
        $this->validUntilSetService = $validUntilSetService;
        $this->userBalanceService = $userBalanceService;
        $this->currentUserService = $currentUserService;
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

    public function makeFeaturedAsDemo(Listing $listing): void
    {
        if (null === $listing->getFeaturedUntilDate()) {
            $this->makeFeatured($listing, 1800);
        }
    }

    public function makeFeaturedByBalance(
        Listing $listing,
        FeaturedPackage $featuredPackage,
        Payment $payment = null
    ): UserBalanceChange {
        $paymentAndListingHaveSameUser = null === $payment || $listing->getUser() === $payment->getUser();
        $listingOfCurrentUser = $listing->getUser() === $this->currentUserService->getUserOrNull();
        if (!$paymentAndListingHaveSameUser) {
            throw new \RuntimeException('payment and listing does not have same user');
        }
        if (!$listingOfCurrentUser) {
            throw new \RuntimeException('not current user listing');
        }

        try {
            $this->em->beginTransaction();
            $cost = $featuredPackage->getPrice();
            if (!$this->userBalanceService->hasAmount($cost, $listing->getUser())) {
                throw new UserVisibleException('trans.error, not enough funds to pay');
            }

            $this->makeFeatured($listing, $featuredPackage->getDaysFeaturedExpire() * 3600 * 24);
            $userBalanceChange = $this->userBalanceService->removeBalance(
                $cost,
                $listing->getUser(),
                $payment,
            );
            $this->validUntilSetService->addValidityDaysWithoutRestrictions(
                $listing,
                $featuredPackage->getDaysListingExpire(),
            );
            $this->preventListingValidDateLessThanFeatured($listing);

            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }

        return $userBalanceChange;
    }

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
        if ($featuredPackage->getDefaultPackage() && !$this->haveCategorySpecificPackage($listing->getCategory())) {
            return true; // we use default featured package
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('featuredPackageForCategory');
        $qb->from(FeaturedPackageForCategory::class, 'featuredPackageForCategory');
        $qb->select($qb->expr()->count('featuredPackageForCategory.id'));
        $qb->join('featuredPackageForCategory.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.featuredPackage', ':featuredPackage'));
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->setParameter(':featuredPackage', $featuredPackage->getId(), Types::INTEGER);
        $qb->setParameter(':category', $listing->getCategory());

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function haveCategorySpecificPackage(Category $category): bool
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('featuredPackageForCategory');
        $qb->from(FeaturedPackageForCategory::class, 'featuredPackageForCategory');
        $qb->select($qb->expr()->count('featuredPackageForCategory.id'));
        $qb->join('featuredPackageForCategory.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->eq('featuredPackageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('featuredPackage.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('featuredPackage.removed', 0));
        $qb->setParameter(':category', $category->getId(), Types::INTEGER);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
