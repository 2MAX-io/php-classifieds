<?php

declare(strict_types=1);

namespace App\Helper;

class IniHelper
{
    /** @noinspection PhpMissingBreakStatementInspection */
    public static function returnBytes(string $value): int
    {
        $value = \trim($value);
        $valueInt = (int) $value;
        $lastCharOfValue = \strtolower($value[\strlen($value) - 1]);

        switch ($lastCharOfValue) {
            /*
             * without break because it multiplies from biggest, all the way to bytes
             */
            case 'g':
                $valueInt *= 1024;
                // no break
            case 'm':
                $valueInt *= 1024;
                // no break
            case 'k':
                $valueInt *= 1024;
        }

        return $valueInt;
    }

    public static function setMemoryLimitIfLessThanMb(int $minMemoryMb): void
    {
        $value = \ini_get('memory_limit');
        if (empty($value)) {
            throw new \RuntimeException('could not get current `memory_limit` by ini_get');
        }

        if (self::returnBytes($value) < MegabyteHelper::toByes($minMemoryMb)) {
            \ini_set('memory_limit', $minMemoryMb.'M');
        }
    }

    public static function setSizeIfLessThanMb(string $iniOptionName, int $minMb): void
    {
        $value = \ini_get($iniOptionName);
        if (empty($value)) {
            throw new \RuntimeException('could not get current `'.$iniOptionName.'` by ini_get');
        }

        if (self::returnBytes($value) < MegabyteHelper::toByes($minMb)) {
            \ini_set($iniOptionName, $minMb.'M');
        }
    }
}
