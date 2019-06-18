<?php /** @noinspection PhpUndefinedClassInspection */

declare(strict_types=1);

namespace App\System\Localization;

class AppNumberFormatter
{
    public static function getFormatter(): \NumberFormatter
    {
        static $formatter;

        if ($formatter instanceof \NumberFormatter) {
            return $formatter;
        }

        $formatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::GROUPING_USED, true);

        return $formatter;
    }

    public static function getDecimalSeparator(): string
    {
        return static::getFormatter()->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
    }

    public static function getThousandSeparator(): string
    {
        return static::getFormatter()->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
    }
}
