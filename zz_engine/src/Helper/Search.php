<?php

declare(strict_types=1);

namespace App\Helper;

class Search
{
    public static function optimizeMatch(string $search): string
    {
        if (\preg_match('#^\S+@\S+\.\S+$#', trim($search))) {
            return '"'. trim($search) .'"';
        }

        if (\preg_match('#^[\d\s]+$#', trim($search))) {
            return '"'. trim($search) .'"' . '' . Str::replace($search, [' '], '');
        }

        $search = Str::replace($search, ['@'], '?');

        return $search;
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
