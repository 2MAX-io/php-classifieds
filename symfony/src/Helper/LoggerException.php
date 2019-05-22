<?php

declare(strict_types=1);

namespace App\Helper;

class LoggerException
{
    public static function flatten(\Throwable $e, array $context = []): array
    {
        return \array_merge(
            [
                'exceptionMessage' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => \substr($e->getTraceAsString() . 0, 500),
            ],
            $context
        );
    }
}
