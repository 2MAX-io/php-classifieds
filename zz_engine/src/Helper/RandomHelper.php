<?php

declare(strict_types=1);

namespace App\Helper;

class RandomHelper
{
    public static function string(
        int $length,
        /* @noinspection SpellCheckingInspection */
        string $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789'
    ): string {
        $alphabetLength = \mb_strlen($alphabet);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $alphabet[\random_int(0, $alphabetLength - 1)];
        }

        return $randomString;
    }

    public static function bool(): bool
    {
        return (bool) \random_int(0, 1);
    }

    public static function float(float $min, float $max, int $minPrecision = 2): float
    {
        $countDecimals = static function (float $decimal) {
            $dotPosition = \strrchr((string) $decimal, '.');
            if (false === $dotPosition) {
                return 0;
            }

            return \strlen(\substr($dotPosition, 1));
        };
        $decimals = \max($minPrecision, $countDecimals($min), $countDecimals($max));
        $factor = 10 ** $decimals;

        return (float) (\random_int((int) ($min * $factor), (int) ($max * $factor)) / $factor);
    }

    /**
     * @param array<int|string,mixed> $array
     *
     * @return array|float|int|mixed|object|string
     */
    public static function fromArray(array $array, int $count = 1)
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
}
