<?php

declare(strict_types=1);

namespace App\Helper;

class Str
{
    public static function beginsWith(string $string, string $beginsWith): bool
    {
        return strpos($string, $beginsWith) === 0;
    }

    public static function contains(string $string, string $needle): bool
    {
        return strpos($string, $needle) !== false;
    }

    public static function containsOneOf(string $string, array $needleList): bool
    {
        foreach ($needleList as $needle) {
            if (strpos($string, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function replace(string $string, array $from, string $to): string
    {
        return \str_replace(
            $from,
            array_fill_keys(array_keys($from), $to),
            $string
        );
    }

    public static function toInt(string $value): int
    {
        return (int) $value;
    }
}
