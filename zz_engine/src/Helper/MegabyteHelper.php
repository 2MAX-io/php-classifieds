<?php

declare(strict_types=1);

namespace App\Helper;

class MegabyteHelper
{
    public static function toByes(int $megabytes): int
    {
        return $megabytes * 1024 * 1024;
    }
}
