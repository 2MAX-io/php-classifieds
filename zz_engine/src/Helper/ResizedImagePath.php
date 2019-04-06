<?php

declare(strict_types=1);

namespace App\Helper;

class ResizedImagePath
{
    public const NORMAL = 'normal';
    public const LIST = 'list';

    public static function forType(string $type, string $path): string
    {
        $type = \basename($type);

        return \str_replace('static/', "static/resized/{$type}/", $path);
    }
}
