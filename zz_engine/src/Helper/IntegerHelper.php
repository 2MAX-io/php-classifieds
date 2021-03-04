<?php

declare(strict_types=1);

namespace App\Helper;

class IntegerHelper
{
    /**
     * @param mixed $value
     */
    public static function toInteger($value): ?int
    {
        if (null === $value) {
            return null;
        }

        return (int) $value;
    }
}
