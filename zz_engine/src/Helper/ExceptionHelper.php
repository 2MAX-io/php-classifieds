<?php

declare(strict_types=1);

namespace App\Helper;

class ExceptionHelper
{
    public static function flatten(\Throwable $e, array $context = []): array
    {
        return \array_merge(
            [
                'exceptionMessage' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => "\n" . \mb_substr($e->getTraceAsString(), 0, 1000) . "\n",
                'previous' => \array_map(
                    static function (\Throwable $e) {
                        return static::flatten($e);
                    },
                    static::getAllPrevious($e)
                ),
            ],
            $context
        );
    }

    /**
     * @return \Throwable[]
     */
    public static function getAllPrevious(\Throwable $e): array
    {
        $exceptions = [];
        while ($e = $e->getPrevious()) {
            $exceptions[] = $e;
        }

        return $exceptions;
    }
}
