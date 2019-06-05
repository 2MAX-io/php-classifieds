<?php

declare(strict_types=1);

namespace App\Helper;

class IniHelper
{
    public static function returnBytes(string $value): int
    {
        $value = \trim($value);
        $valueInt = (int) $value;
        $last = \strtolower($value[\strlen($value)-1]);
        switch($last) {
            /**
             * without break because it multiplies from biggest, all the way to bytes
             */
            /** @noinspection PhpMissingBreakStatementInspection */ case 'g':
                $valueInt *= 1024;
            /** @noinspection PhpMissingBreakStatementInspection */ case 'm':
                $valueInt *= 1024;
            case 'k':
                $valueInt *= 1024;
        }

        return $valueInt;
    }
}
