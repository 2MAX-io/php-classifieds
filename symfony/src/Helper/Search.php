<?php

declare(strict_types=1);

namespace App\Helper;

class Search
{
    public static function optimize(string $search): string
    {
        return rtrim($search, '*') .'*';
    }
}
