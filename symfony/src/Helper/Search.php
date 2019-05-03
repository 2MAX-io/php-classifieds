<?php

declare(strict_types=1);

namespace App\Helper;

class Search
{
    public static function optimizeMatch(string $search): string
    {
        $hasWildcard = Str::containsOneOf($search, ['*', '?']);
        $search = Str::replace($search, ['@'], '?');

        if ($hasWildcard) {
            return $search;
        } else {
            return \implode('* ', \explode(' ', $search));
        }
    }

    public static function optimizeLike(string $search): string
    {
        $return = $search;
        $hasWildcard = Str::containsOneOf($search, ['*', '?']);
        $return = \str_replace('*', '%', $return);
        $return = \str_replace('?', '_', $return);

        if ($hasWildcard) {
            return $return;
        } else {
            return '%' . $return . '%';
        }
    }
}
