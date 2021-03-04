<?php

declare(strict_types=1);

namespace App\Helper;

class SearchHelper
{
    public static function optimizeMatch(string $search): string
    {
        $newSearch = $search;
        $newSearch = \rtrim($newSearch, '-'); // fixes incorrect search value
        $newSearch = \trim($newSearch);
        if ('+' === $newSearch) {
            return ''; // fixes incorrect search value
        }

        $isEmail = \preg_match('~^\S+@\S+\.\S+$~', \trim($newSearch));
        if ($isEmail) {
            return '"'.$newSearch.'"'; // email quoted for better search
        }

        $isPhoneNumber = \preg_match('~^[\d\s]+$~', $newSearch); // digits and spaces
        if ($isPhoneNumber) {
            return '"'.$newSearch.'"'.' '.StringHelper::replaceMultipleToSingle($newSearch, [' '], '');
        }

        return StringHelper::replaceMultipleToSingle($newSearch, ['@'], '?');
    }

    public static function optimizeLike(string $search): string
    {
        $return = $search;
        $hasWildcard = StringHelper::containsOneOf($search, ['*', '?', '%']);
        $return = StringHelper::replaceMultiple($return, [
            '*' => '%',
            '?' => '_',
        ]);

        if ($hasWildcard) {
            return $return;
        }

        return '%'.$return.'%';
    }
}
