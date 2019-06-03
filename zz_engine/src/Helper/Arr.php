<?php

declare(strict_types=1);

namespace App\Helper;

class Arr
{
    /**
     * @param mixed $needle
     */
    public static function inArray($needle, array $haystack): bool
    {
        return \in_array($needle, $haystack, true);
    }

    public static function valuesToInt(array $array): array
    {
        return \array_map('intval', $array);
    }

    /**
     * @return array|string|int|mixed
     */
    public static function random(array $array, int $count = 1)
    {
        if ($count < 1) {
            throw new \UnexpectedValueException('count should be more than 0');
        }

        if (\count($array) < 1) {
            throw new \UnexpectedValueException('array is empty');
        }

        if (1 === $count) {
            return $array[\array_rand($array)];
        }

        $keys = \array_rand($array, $count);
        $results = [];
        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * @param callable $callback returns [key => value]
     */
    public static function indexBy(array $array, callable $callback): array
    {
        $return = [];
        while ($element = \array_pop($array)) {
            $result = $callback($element);
            $return[\key($result)] = \current($result);
        }

        return $return;
    }
}
