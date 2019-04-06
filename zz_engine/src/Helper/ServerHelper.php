<?php

declare(strict_types=1);

namespace App\Helper;

class ServerHelper
{
    public static function getServerAsString(): string
    {
        $serverKeysToGet = [
            'REQUEST_URI',
            'REQUEST_METHOD',
            'HTTP_USER_AGENT',
            'SERVER_ADDR',
            'SERVER_PORT',
            'REMOTE_ADDR',
            'REMOTE_PORT',
            'REMOTE_HOST',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CF_RAY',
            'HTTP_CF_VISITOR',
            'HTTP_ORIGIN',
            'SERVER_NAME',
            'HTTP_HOST',
            'SERVER_PROTOCOL',
            'HTTP_REFERER',
            'REQUEST_TIME_FLOAT',
        ];

        $return = '';
        foreach ($serverKeysToGet as $serverKey) {
            if (!isset($_SERVER[$serverKey])) {
                continue;
            }

            $return .= "{$serverKey} => {$_SERVER[$serverKey]} \r\n";
        }

        return $return;
    }
}
