<?php

declare(strict_types=1);

namespace App\Helper;

class Arr
{
    public static function inArray($needle, array $haystack)
    {
        return \in_array($needle, $haystack, true);
    }

    public static function random(array $array, int $count = 1)
    { // todo: handle empty array, count less than 1 (zero or minus)
        if (1 === $count) {
            return $array[array_rand($array)];
        }

        $keys = array_rand($array, $count);
        $results = [];
        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }
}
