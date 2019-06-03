<?php

declare(strict_types=1);

namespace App\Helper;

class Integer
{
    /**
     * @param mixed $value
     */
    public static function toInteger($value): int
    {
        return (int) $value;
    }
}
