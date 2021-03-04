<?php

declare(strict_types=1);

namespace App\Helper;

class BoolHelper
{
    /**
     * @param mixed $value
     */
    public static function isTrue($value, bool $castToBool = false): bool
    {
        if (true === $value) {
            return true;
        }

        if (1 === $value) {
            return true;
        }

        $valueTrimmed = \trim((string) $value);

        if ('1' === $valueTrimmed) {
            return true;
        }

        if ('true' === $valueTrimmed) {
            return true;
        }

        if (false === $value) {
            return false;
        }

        if (0 === $value) {
            return false;
        }

        if (null === $value) {
            return false;
        }

        if ('' === $value) {
            return false;
        }

        if ('0' === $valueTrimmed) {
            return false;
        }

        if ('false' === $valueTrimmed) {
            return false;
        }

        if ($castToBool) {
            return (bool) $valueTrimmed;
        }

        return false;
    }
}
