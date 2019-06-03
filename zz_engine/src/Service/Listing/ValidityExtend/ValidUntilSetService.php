<?php

declare(strict_types=1);

namespace App\Service\Listing\ValidityExtend;

use App\Entity\Listing;
use App\Helper\Date;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Doctrine\Common\Persistence\ObjectManager;

class ValidUntilSetService
{
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(ObjectManager $em)
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

        $listing->setValidUntilDate($newValidUntilDate);
        $this->em->persist($listing);
    }

    public function validityExtendedByUser(Listing $listing): void
    {
        $listing->setUserDeactivated(false);

        if (Date::olderThanDays(10, $listing->getOrderByDate())) {
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
