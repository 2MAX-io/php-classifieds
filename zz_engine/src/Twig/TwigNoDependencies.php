<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\Str;
use App\System\Localization\AppNumberFormatter;
use Carbon\Carbon;
use DateTimeInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigNoDependencies implements RuntimeExtensionInterface
{
    public function displayTextWarning(bool $bool): string
    {
        return $bool ? 'text-warning-color' : '';
    }

    public function boolGreenRedClass(bool $bool): string
    {
        return $bool ? 'text-success' : 'text-danger';
    }

    public function isExpired(\DateTime $date): bool
    {
        return $date <= new \DateTime();
    }

    /**
     * @param mixed $value
     */
    public function boolText($value): string
    {
        if ($value === true) {
            return 'trans.Yes';
        }

        if ($value === false) {
            return 'trans.No';
        }

        if ($value === '0' || $value === '' || $value === 'false' || $value === 'null') {
            return 'trans.No';
        }

        if (Str::emptyTrim($value)) {
            return 'trans.No';
        }

        if ($value === '1' || $value === 1 || $value === 'true') {
            return 'trans.Yes';
        }

        if ($value) {
            return 'trans.Yes';
        }

        return 'trans.No';
    }

    public function moneyPrecise(int $value): string
    {
        return \number_format(
            \round($value / 100, 2),
            2,
            AppNumberFormatter::getDecimalSeparator(),
            AppNumberFormatter::getThousandSeparator()
        );
    }

    public function thousandsSeparate(int $value): string
    {
        return \number_format(
            $value,
            0,
            AppNumberFormatter::getDecimalSeparator(),
            AppNumberFormatter::getThousandSeparator()
        );
    }

    public function money(float $money): string
    {
        if ($money < 40) {
            return (string) \round($money, 2);
        }

        return \number_format(
            $money,
            0,
            AppNumberFormatter::getDecimalSeparator(),
            AppNumberFormatter::getThousandSeparator()
        );
    }

    public function returnPlusIfPositive(float $number): string
    {
        if ($number > 0) {
            return '+';
        }

        return '';
    }

    public function getCleaveConfig(): array
    {
        return [
            'delimiter' => AppNumberFormatter::getThousandSeparator(),
            'numeralDecimalMark' => AppNumberFormatter::getDecimalSeparator(),
        ];
    }

    public function normalizeWhitespace(string $string): string
    {
        return \trim(\preg_replace('/\s+/u', ' ', $string));
    }

    /**
     * diffToNowWithinSeconds(listing.featuredUntilDate, -1800, 0)
     * true if less than 1800 seconds to now
     */
    public function diffToNowWithinSeconds(?DateTimeInterface $dateTime, int $maxSecondsLeft, int $maxSecondsRight=0): bool
    {
        if (null === $dateTime) {
            return false;
        }
        $diffInSeconds = Carbon::instance($dateTime)->diffInSeconds(Carbon::now(), false);

        return $maxSecondsLeft < $diffInSeconds && $diffInSeconds < $maxSecondsRight;
    }
}
