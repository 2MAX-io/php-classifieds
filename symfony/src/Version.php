<?php

declare(strict_types=1);

namespace App;

class Version
{
    const VERSION = 0;
    const DATE = '2019-04-06';

    public static function getVersion(): int
    {
        return static::VERSION;
    }

    public static function getDate(): string
    {
        return static::DATE;
    }
}
