<?php

declare(strict_types=1);

namespace App\Service\Listing\ValidityExtend;

use App\Entity\Listing;
use App\Helper\DateHelper;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class ValidUntilSetService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setValidityDaysFromNow(Listing $listing, int $validityTimeDays): void
    {
        $validityTimeDays = \min($validityTimeDays, $this->getMaxValidityTimeDays());
        $newValidUntilDate = DateHelper::carbonNow()->addDays($validityTimeDays);
        if ($newValidUntilDate < $listing->getValidUntilDate()) {
            return;
        }
        $newValidUntilDate->setTime(23, 59, 59);

        $listing->setValidUntilDate($newValidUntilDate);
        $this->em->persist($listing);
    }

    public function onValidityExtendedByUser(Listing $listing): void
    {
        $listing->setUserDeactivated(false);
        $this->pullUpOrderListing($listing);
    }

    public function addValidityDaysWithoutRestrictions(Listing $listing, int $validityTimeDays): void
    {
        $currentValidUntilDate = Carbon::instance($listing->getValidUntilDate());
        $newValidUntilDate = $currentValidUntilDate->addDays($validityTimeDays);
        $listing->setValidUntilDate($newValidUntilDate);
        $this->em->persist($listing);
    }

    /**
     * @return int[]
     */
    public function getValidityTimeDaysChoices(): array
    {
        return [
            'trans.1 week' => 9,
            'trans.2 weeks' => 14,
            'trans.3 weeks' => 21,
            'trans.1 month' => 31,
        ];
    }

    private function getMaxValidityTimeDays(): int
    {
        $max = \max($this->getValidityTimeDaysChoices());
        if (false === $max) {
            throw new \RuntimeException('max validity time not found');
        }

        return $max;
    }

    private function pullUpOrderListing(Listing $listing): void
    {
        if (DateHelper::olderThanDays(40, $listing->getOrderByDate())) {
            $listing->setOrderByDate(DateHelper::create());
        }
    }
}
