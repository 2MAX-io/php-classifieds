<?php

declare(strict_types=1);

namespace App\Helper;

class Megabyte
{
    public static function toByes(int $megabytes): int
    {
        return $megabytes * 1024 * 1024;
    }
}
