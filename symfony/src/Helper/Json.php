<?php

declare(strict_types=1);

namespace App\Helper;

class Json
{
    public static function decodeToArray(string $jsonString): array
    {
        return \json_decode($jsonString, true);
    }
}
