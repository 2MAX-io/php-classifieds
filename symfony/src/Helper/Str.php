<?php

declare(strict_types=1);

namespace App\Helper;

class Str
{
    public static function beginsWith(string $string, string $beginsWith): bool
    {
        return strpos($string, $beginsWith) === 0;
    }

    public static function contains(string $string, string $needle): bool
    {
        return strpos($string, $needle) !== false;
    }

    public static function containsOneOf(string $string, array $needleList): bool
    {
        foreach ($needleList as $needle) {
            if (strpos($string, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function replace(string $string, array $from, string $to): string
    {
        return \str_replace(
            $from,
            array_fill_keys(array_keys($from), $to),
            $string
        );
    }

    public static function replaceIgnoreCase(string $string, array $from, string $to): string
    {
        return \str_ireplace(
            $from,
            array_fill_keys(array_keys($from), $to),
            $string
        );
    }

    public static function toInt(string $value): int
    {
        return (int) $value;
    }

    public static function match(string $pattern, $subject): ?array
    {
        $matches = [];
        $result = preg_match_all($pattern, $subject, $matches);

        if ($result === false) {
            throw new \Exception('preg_match error' . \preg_last_error());
        }

        if ($result !== 1) {
            return null;
        }

        return $matches;
    }

    public static function emptyTrim($value): bool
    {
        if ($value === '0') {
            return false;
        }

        if (\is_bool($value)) {
            return false;
        }

        if ($value === null) {
            return true;
        }

        return empty($value) || empty(trim($value));
    }

    public static function toString($value): string
    {
        if (false === $value) {
            return '0';
        }
        if (true === $value) {
            return '1';
        }

        if (null === $value) {
            return '';
        }

        return (string) $value;
    }

    public static function softSlug(?string $value): ?string
    {
        if (null === $value) {
            return $value;
        }

        $value = \mb_strtolower($value);
        $value = Str::replace($value, [' '], '-');

        return $value;
    }
}
