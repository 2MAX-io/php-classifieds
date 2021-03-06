<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class JsonHelper
{
    /**
     * @return array<string,mixed>|null
     */
    public static function toArray(?string $jsonString): ?array
    {
        if (StringHelper::emptyTrim($jsonString)) {
            return null;
        }

        return \json_decode($jsonString, true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string,array|int|string> $default
     *
     * @return array<string,array|int|string>|null
     */
    public static function toArrayFromRequestString(string $requestValue, array $default = []): ?array
    {
        return static::toArray($requestValue) ?? $default;
    }

    /**
     * @param array<string,array|int|string> $default
     *
     * @return array<string,array|int|string>
     */
    public static function toArrayFromRequestKey(Request $request, string $requestKey, array $default = []): array
    {
        return static::toArray($request->get($requestKey)) ?? $default;
    }

    /**
     * @param array<mixed> $array
     */
    public static function toString(array $array): string
    {
        $jsonString = \json_encode($array, \JSON_PRETTY_PRINT & \JSON_THROW_ON_ERROR);
        if (false === $jsonString) {
            throw new \RuntimeException('can not create json string');
        }

        return $jsonString;
    }
}
