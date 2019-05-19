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
        return self::getPath(__DIR__ . '/../../../static/listing');
    }

    public static function getStaticPath(): string
    {
        return self::getPath(__DIR__ . '/../../../static/');
    }

    public static function getLogDir(): string
    {
        return self::getPath(__DIR__ . '/../../../symfony/var/log');
    }

    public static function getCacheDir(): string
    {
        return self::getPath(__DIR__ . '/../../../symfony/var/cache');
    }

    public static function getProjectDir(): string
    {
        return self::getPath(__DIR__ . '/../../../');
    }

    public static function getCategoryPicturePath()
    {
        return self::getPath(__DIR__ . '/../../../static/category');
    }

    public static function getLogoPath()
    {
        return self::getPath(__DIR__ . '/../../../static/logo');
    }

    public static function getFile(string $filePath): string
    {
        return $filePath;
    }
}
