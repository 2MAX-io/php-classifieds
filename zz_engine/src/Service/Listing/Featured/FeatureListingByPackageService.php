<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\Listing;
use App\Entity\Package;
use App\Entity\Payment;
use App\Entity\UserBalanceChange;
use App\Exception\UserVisibleException;
use App\Service\Listing\Package\ApplyPackageToListingService;
use App\Service\Money\UserBalanceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class FeatureListingByPackageService
{
    /**
     * @var ApplyPackageToListingService
     */
    private $applyPackageToListingService;

    /**
     * @var UserBalanceService
     */
    private $userBalanceService;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    public function __construct(
        ApplyPackageToListingService $applyPackageToListingService,
        UserBalanceService $userBalanceService,
        EntityManagerInterface $em
    ) {
        $this->applyPackageToListingService = $applyPackageToListingService;
        $this->userBalanceService = $userBalanceService;
        $this->em = $em;
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

            $userBalanceChange = $this->userBalanceService->removeBalance(
                $cost,
                $listing->getUser(),
                $payment,
            );
            $this->applyPackageToListingService->applyPackageToListing($listing, $package, true);

            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }

        return $userBalanceChange;
    }

    public function makeFeaturedFree(Listing $listing, Package $package): void
    {
        try {
            $this->em->beginTransaction();
            if ($package->isPaidPackage()) {
                throw new \RuntimeException('Can not feature for free using paid package');
            }

            $this->applyPackageToListingService->applyPackageToListing($listing, $package);

            if ($package->getDemoPackage()) {
                $user = $listing->getUserNotNull();
                $user->setDemoPackageUsed(true);
                $this->em->persist($user);
            }

            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();

            throw $e;
        }
    }
}
