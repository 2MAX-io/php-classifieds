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

    public static function toInt(string $value): int
    {
        return (int) $value;
    }
}
