<?php

declare(strict_types=1);

namespace App\Helper;

use Carbon\Carbon;

class DateHelper
{
    public static function create(): \DateTime
    {
        return new \DateTime();
    }

    public static function createFromSqlString(string $datetime): \DateTime
    {
        return new \DateTime($datetime);
    }

    public static function date(string $datetime): ?string
    {
        return \date($datetime);
    }

    public static function carbonNow(): Carbon
    {
        return Carbon::now();
    }

    public static function timestamp(): int
    {
        return \time();
    }

    public static function olderThanDays(int $days, \DateTimeInterface $dateTime): bool
    {
        return $dateTime < static::carbonNow()->subDays($days);
    }

    public static function fromMicroTimeFloat(float $microTimeFloat): \DateTime
    {
        $dateTime = null;
        $microTimeFloatString = (string) $microTimeFloat;
        if (\strpos($microTimeFloatString, '.')) {
            $dateTime = \DateTime::createFromFormat('U.u', $microTimeFloatString);
        }

        if (!$dateTime) {
            $dateTime = \DateTime::createFromFormat('U', $microTimeFloatString);
        }

        if (!$dateTime) {
            throw new \RuntimeException("could not generate datetime from microTimeFloat: `{$microTimeFloat}`");
        }

        return $dateTime;
    }
}
