<?php

declare(strict_types=1);

namespace App\Helper;

class ArrayHelper
{
    /**
     * @param mixed $needle
     * @param array<int|string,mixed> $haystack
     */
    public static function inArray($needle, array $haystack): bool
    {
        return \in_array($needle, $haystack, true);
    }

    /**
     * @param array<int|string, mixed> $array
     *
     * @return array<int|string, mixed>
     */
    public static function valuesToInt(array $array): array
    {
        return \array_map('\intval', $array);
    }

    /**
     * @param array<int|string, mixed> $array
     * @param callable $callback returns [key => value]
     *
     * @return array<int|string, mixed>
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
