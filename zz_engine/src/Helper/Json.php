<?php

declare(strict_types=1);

namespace App\Helper;

class Json
{
    public static function toArray(string $jsonString): ?array
    {
        return \json_decode($jsonString, true, 512, \JSON_THROW_ON_ERROR);
    }

    public static function toString(array $array): string
    {
        return \json_encode($array, \JSON_PRETTY_PRINT & \JSON_THROW_ON_ERROR);
    }
}
