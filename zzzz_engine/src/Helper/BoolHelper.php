<?php

declare(strict_types=1);

namespace App\Helper;

class BoolHelper
{
    public static function isTrue($value, bool $castToBool = false): bool
    {
        if ($value === true) {
            return true;
        }

        if ($value === 1) {
            return true;
        }

        $valueTrimmed = \trim((string) $value);

        if ($valueTrimmed === '1') {
            return true;
        }

        if ($valueTrimmed === 'true') {
            return true;
        }

        if ($value === false) {
            return false;
        }

        if ($value === 0) {
            return false;
        }

        if ($value === null) {
            return false;
        }

        if ($value === '') {
            return false;
        }

        if ($valueTrimmed === '0') {
            return false;
        }

        if ($valueTrimmed === 'false') {
            return false;
        }

        if ($castToBool) {
            return (bool) $valueTrimmed;
        }

        return false;
    }
}
