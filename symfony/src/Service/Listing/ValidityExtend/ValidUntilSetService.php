<?php

declare(strict_types=1);

namespace App\Service\Listing\ValidityExtend;

use App\Entity\Listing;
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

    public function setValidUntil(Listing $listing, int $validityTimeDays)
    {
        if (!$listing->getAdminConfirmed()) {
            return;
        }

        $validityTimeDays = min($validityTimeDays, $this->getMaxValidityTimeDays());

        $listing->setValidUntilDate(Carbon::now()->add(CarbonInterval::days($validityTimeDays)));
    }

    public function getValidityTimeDaysChoices(): array
    {
        return [
            'trans.1 week' => 9,
            'trans.2 weeks' => 14,
            'trans.3 weeks' => 21,
            'trans.31 days' => 31,
        ];
    }

    public function getMaxValidityTimeDays(): int
    {
        return max($this->getValidityTimeDaysChoices());
    }
}
