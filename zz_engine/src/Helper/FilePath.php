<?php

declare(strict_types=1);

namespace App\Helper;

use Webmozart\PathUtil\Path;

class FilePath
{
    public static function getPath(string $relativePath): string
    {
        $absolute = Path::canonicalize($relativePath);
        $projectDir = Path::canonicalize(__DIR__.'/../../../');

        if (Path::getLongestCommonBasePath([$absolute, $projectDir]) !== $projectDir) {
            throw new \UnexpectedValueException('path is outside of project');
        }

        return $absolute;
    }

    public static function getListingFilePath(): string
    {
        return self::getPath(__DIR__.'/../../../static/listing');
    }

    public static function getStaticPath(): string
    {
        return self::getPath(__DIR__.'/../../../static/');
    }

    public static function getLogDir(): string
    {
        return self::getPath(__DIR__.'/../../../zz_engine/var/log');
    }

    public static function getCacheDir(): string
    {
        return self::getPath(__DIR__.'/../../../zz_engine/var/cache');
    }

    public static function getUpgradeDir(): string
    {
        return self::getPath(__DIR__.'/../../../zz_engine/var/cache/upgrade');
    }

    public static function getProjectDir(): string
    {
        return self::getPath(__DIR__.'/../../../');
    }

    public static function getPublicDir(): string
    {
        return self::getPath(__DIR__.'/../../../');
    }

    public static function getEngineDir(): string
    {
        return self::getPath(__DIR__.'/../../../zz_engine/');
    }

    public static function getCategoryPicturePath(): string
    {
        return self::getPath(__DIR__.'/../../../static/category');
    }

    public static function getLogoPath(): string
    {
        return self::getPath(__DIR__.'/../../../static/logo');
    }

    public static function getTempFileUpload(): string
    {
        return self::getPath(__DIR__.'/../../../static/tmp/file_upload');
    }

    public static function getAssetBuildDir(): string
    {
        return self::getPath(__DIR__.'/../../../asset/build');
    }
}
