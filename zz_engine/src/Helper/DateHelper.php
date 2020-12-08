<?php

declare(strict_types=1);

namespace App\Helper;

use Carbon\Carbon;

class DateHelper
{
    public static function create(): \DateTimeInterface
    {
        return new \DateTime();
    }

    public static function olderThanDays(int $days, \DateTimeInterface $dateTime): bool
    {
        return $dateTime < Carbon::now()->subDays($days);
    }

    public static function fromMicroTimeFloat(float $microTimeFloat): \DateTimeInterface
    {
        $microTimeFloatString = (string) $microTimeFloat;

        if (\is_numeric($microTimeFloat) && \strpos($microTimeFloatString, '.')) {
            return \DateTime::createFromFormat('U.u', $microTimeFloatString);
        }

        return \DateTime::createFromFormat('U', $microTimeFloatString);
    }
}
