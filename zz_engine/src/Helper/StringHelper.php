<?php

declare(strict_types=1);

namespace App\Helper;

class StringHelper
{
    public static function beginsWith(string $string, string $beginsWith): bool
    {
        return 0 === \strpos($string, $beginsWith);
    }

    /**
     * @param string[] $needleList
     */
    public static function containsOneOf(string $string, array $needleList): bool
    {
        foreach ($needleList as $needle) {
            if (false !== \strpos($string, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $from
     */
    public static function replaceMultipleToSingle(string $string, array $from, string $to): string
    {
        return \str_replace(
            $from,
            \array_fill_keys(\array_keys($from), $to),
            $string
        );
    }

    /**
     * @param array<int|string,int|string> $replace
     */
    public static function replaceMultiple(string $string, array $replace): string
    {
        return \str_replace(
            \array_keys($replace),
            \array_values($replace),
            $string
        );
    }

    /**
     * @param string[] $from
     */
    public static function replaceIgnoreCase(string $string, array $from, string $to): string
    {
        return \str_ireplace(
            $from,
            \array_fill_keys(\array_keys($from), $to),
            $string
        );
    }

    public static function toInt(string $value): int
    {
        return (int) $value;
    }

    /**
     * @return null|string[][]
     */
    public static function match(string $pattern, string $subject): ?array
    {
        $matches = [];
        $result = \preg_match_all($pattern, $subject, $matches);

        if (false === $result) {
            throw new \UnexpectedValueException('preg_match error'.\preg_last_error());
        }

        if (1 !== $result) {
            return null;
        }

        return $matches;
    }

    /**
     * returns first match or one with name return
     *
     * @param string $pattern ~(?P<return>[\d]+)~
     */
    public static function matchSingle(string $pattern, string $subject): ?string
    {
        $matches = [];
        $result = \preg_match($pattern, $subject, $matches);

        if (false === $result) {
            throw new \UnexpectedValueException('preg_match error: '.\preg_last_error());
        }

        if (1 !== $result) {
            return null;
        }

        if (\array_key_exists('return', $matches)) {
            return $matches['return'];
        }

        if (\array_key_exists(1, $matches) && 2 === \count($matches) && isset($matches[0], $matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param mixed $value
     */
    public static function emptyTrim($value): bool
    {
        if ('0' === $value) {
            return false;
        }

        if (\is_bool($value)) {
            return false;
        }

        if (null === $value) {
            return true;
        }

        if (\is_numeric($value)) {
            return empty($value);
        }

        return empty($value) || empty(\trim($value));
    }

    /**
     * @param mixed $value
     */
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

    /**
     * @return bool|resource
     */
    public static function toStream(string $string)
    {
        $stream = \fopen('php://memory', 'rb+');
        if (false === $stream) {
            throw new \RuntimeException('could not open stream php://memory');
        }
        \fwrite($stream, $string);
        \rewind($stream);

        return $stream;
    }

    public static function substrWords(string $string, int $maxLength): string
    {
        $forcedSubstring = \mb_substr($string, 0, $maxLength + 1);

        return \mb_substr($forcedSubstring, 0, \strrpos($forcedSubstring, ' ') ?: null);
    }
}
