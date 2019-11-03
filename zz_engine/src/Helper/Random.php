<?php

declare(strict_types=1);

namespace App\Helper;

class Random
{
    public static function string(int $length, /** @noinspection SpellCheckingInspection */ string $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789'): string
    {
        $max = \mb_strlen($alphabet);

        $token = '';
        for ($i=0; $i < $length; $i++) {
            $token .= $alphabet[\random_int(0, $max-1)];
        }

        return $token;
    }

    public static function bool(): bool
    {
        return (bool) \random_int(0, 1);
    }
}
