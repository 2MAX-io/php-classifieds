<?php

declare(strict_types=1);

namespace App\Service\Listing\ValidityExtend;

use App\Entity\Listing;
use App\Helper\DateHelper;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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
        $newValidUntilDate = Carbon::now()->add(CarbonInterval::days($validityTimeDays));
        if ($newValidUntilDate < $listing->getValidUntilDate()) {
            return;
        }
        $newValidUntilDate->setTime(23, 59, 59);

        $listing->setValidUntilDate($newValidUntilDate);
        $this->em->persist($listing);
    }

    public function validityExtendedByUser(Listing $listing): void
    {
        $listing->setUserDeactivated(false);

        if (DateHelper::olderThanDays(40, $listing->getOrderByDate())) {
            $listing->setOrderByDate(new \DateTime());
        }
    }

    public function addValidityDaysWithoutRestrictions(Listing $listing, int $validityTimeDays): void
    {
        $newValidUntilDate = Carbon::instance($listing->getValidUntilDate())->add(CarbonInterval::days($validityTimeDays));
        $listing->setValidUntilDate($newValidUntilDate);
        $this->em->persist($listing);
    }

    public function getValidityTimeDaysChoices(): array
    {
        return [
            'trans.1 week' => 9,
            'trans.2 weeks' => 14,
            'trans.3 weeks' => 21,
            'trans.1 month' => 31,
        ];
    }

    public function getMaxValidityTimeDays(): int
    {
        return \max($this->getValidityTimeDaysChoices());
    }
}
