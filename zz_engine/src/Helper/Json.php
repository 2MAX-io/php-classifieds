<?php

declare(strict_types=1);

namespace App\Helper;

class Json
{
    public static function decodeToArray(string $jsonString): ?array
    {
        return \json_decode($jsonString, true);
    }

    public static function jsonEncode(array $array): string
    {
        return \json_encode($array, JSON_PRETTY_PRINT);
    }
}
