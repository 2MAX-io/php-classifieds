<?php

declare(strict_types=1);

namespace App\Helper;

class Str
{
    public static function beginsWith(string $string, string $beginsWith): bool
    {
        return strpos($string, $beginsWith) === 0;
    }
}
