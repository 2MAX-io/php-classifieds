<?php

declare(strict_types=1);

namespace App;

class Version
{
    public const VERSION = 1;
    public const DATE = '2021-01-01';

    public static function getVersion(): int
    {
        return static::VERSION;
    }

    public static function getDate(): string
    {
        return static::DATE;
    }
}
