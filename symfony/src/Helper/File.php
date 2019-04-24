<?php

declare(strict_types=1);

namespace App\Helper;

use Webmozart\PathUtil\Path;

class File
{
    public static function isImage(string $path): bool
    {
        return in_array(
            Path::getExtension($path, true),
            ['jpg', 'png', 'gif', 'jpeg'],
            true
        );
    }
}
