<?php

declare(strict_types=1);

namespace App\Helper;

use Webmozart\PathUtil\Path;

class FilePath
{
    public static function getPath(string $relativePath): string
    {
        return Path::canonicalize($relativePath); // todo: #12 add path validation, make path absolute
    }

    public static function getListingFilePath(): string
    {
        return self::getPath(__DIR__ . '/../../../static/user/listing');
    }

    public static function getStaticPath(): string
    {
        return self::getPath(__DIR__ . '/../../../static/');
    }

    public static function getProjectDir(): string
    {
        return self::getPath(__DIR__ . '/../../../');
    }
}
