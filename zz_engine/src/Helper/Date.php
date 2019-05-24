<?php

declare(strict_types=1);

namespace App\Helper;

use Carbon\Carbon;

class Date
{
    public static function olderThanDays(int $days, \DateTimeInterface $dateTime): bool
    {
        return $dateTime < Carbon::now()->subDays($days);
    }
}
