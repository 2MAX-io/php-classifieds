<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Category;
use App\Entity\Listing;
use App\Entity\Package;
use App\Entity\PackageForCategory;
use App\Entity\Payment;
use App\Entity\UserBalanceChange;
use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
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
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    public function __construct(
        UserBalanceService $userBalanceService,
        ValidUntilSetService $validUntilSetService,
        EntityManagerInterface $em
    ) {
        $this->validUntilSetService = $validUntilSetService;
        $this->userBalanceService = $userBalanceService;
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
        Package $package,
        Payment $payment = null
    ): UserBalanceChange {
        $paymentAndListingHaveSameUser = null === $payment || $listing->getUser() === $payment->getUser();
        if (!$paymentAndListingHaveSameUser) {
            throw new \RuntimeException('payment and listing does not have same user');
        }

        try {
            $this->em->beginTransaction();
            $cost = $package->getPrice();
            if (!$this->userBalanceService->hasAmount($cost, $listing->getUser())) {
                throw new UserVisibleException('trans.error, not enough funds to pay');
            }

            $this->makeFeatured($listing, $package->getDaysFeaturedExpire() * 3600 * 24);
            $userBalanceChange = $this->userBalanceService->removeBalance(
                $cost,
                $listing->getUser(),
                $payment,
            );
            $this->validUntilSetService->addValidityDaysWithoutRestrictions(
                $listing,
                $package->getDaysListingExpire(),
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

    public function hasAmount(Listing $listing, Package $package): bool
    {
        $cost = $package->getPrice();

        return $this->userBalanceService->hasAmount($cost, $listing->getUser());
    }

    public function isPackageForListingCategory(Listing $listing, Package $package): bool
    {
        if ($package->getDefaultPackage() && !$this->haveCategorySpecificPackage($listing->getCategory())) {
            return true; // we use default package
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('packageForCategory');
        $qb->from(PackageForCategory::class, 'packageForCategory');
        $qb->select($qb->expr()->count('packageForCategory.id'));
        $qb->join('packageForCategory.package', 'package');
        $qb->andWhere($qb->expr()->eq('packageForCategory.package', ':package'));
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->setParameter(':package', $package->getId(), Types::INTEGER);
        $qb->setParameter(':category', $listing->getCategory());

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function haveCategorySpecificPackage(Category $category): bool
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('packageForCategory');
        $qb->from(PackageForCategory::class, 'packageForCategory');
        $qb->select($qb->expr()->count('packageForCategory.id'));
        $qb->join('packageForCategory.package', 'package');
        $qb->andWhere($qb->expr()->eq('packageForCategory.category', ':category'));
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 0));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $qb->setParameter(':category', $category->getId(), Types::INTEGER);

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
