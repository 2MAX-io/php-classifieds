<?php

declare(strict_types=1);

namespace App\System\Filesystem;

use App\Helper\FilePath;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FilesystemChecker
{
    public static function incorrectFilePermissionList(): array
    {
        $return = [];

        $finder = new Finder();
        $finder->in(FilePath::getProjectDir());
        $finder->ignoreUnreadableDirs();
        $finder->exclude([
            'static',
            'zz_engine/docker/',
        ]);

        $fileIterator = $finder->files()->getIterator();
        $i = 0;
        foreach ($fileIterator as $file) {
            if (!is_writable($file->getPathname())
                || !\is_readable($file->getPathname())
                || false === @\file_get_contents($file->getPathname(), false, null, 0, 1)
            ) {
                ++$i;
                if ($i > 5000) {
                    break;
                }
                $return[] = $file->getPathname();
            }
        }

        return $return;
    }

    public static function incorrectDirPermissionList(): array
    {
        $return = [];

        $finder = new Finder();
        $finder->in(FilePath::getProjectDir());
        $finder->ignoreUnreadableDirs();
        $finder->exclude([
            'static',
            'zz_engine/docker/',
        ]);

        $dirIterator = $finder->directories()->getIterator();
        $i = 0;
        foreach($dirIterator as $dir) {
            if (!is_writable($dir->getPathname()) || !\is_readable($dir->getPathname())) {
                ++$i;
                if ($i > 5000) {
                    break;
                }
                $return[] = $dir->getPathname();
            }
        }

        return $return;
    }

    public static function creatingDirFailedList(): array
    {
        $filesystem = new Filesystem();
        if (!\file_exists(FilePath::getProjectDir() . '/zz_engine/var/cache/prod')) {
            $filesystem->mkdir(FilePath::getProjectDir() . '/zz_engine/var/cache/prod', 0750);
        }

        $return = [];
        $patchList = [
            FilePath::getProjectDir() . '/static/cache/test',
            FilePath::getProjectDir() . '/static/listing/test',
            FilePath::getProjectDir() . '/static/resized/test',
            FilePath::getProjectDir() . '/static/tmp/file_upload',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod_test',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod/test',
            FilePath::getProjectDir() . '/zz_engine/var/log/test',
        ];

        foreach ($patchList as $patch) {
            if (!static::canCreateDir($patch)) {
                $return[] = $patch;
            }
        }

        return $return;
    }

    public static function writingFileFailedList(): array
    {
        $return = [];
        $patchList = [
            FilePath::getProjectDir() . '/static/cache',
            FilePath::getProjectDir() . '/static/category',
            FilePath::getProjectDir() . '/static/listing',
            FilePath::getProjectDir() . '/static/logo/',
            FilePath::getProjectDir() . '/static/resized',
            FilePath::getProjectDir() . '/static/tmp/file_upload',
            FilePath::getProjectDir() . '/zz_engine/var/cache',
            FilePath::getProjectDir() . '/zz_engine/var/cache/upgrade',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod',
            FilePath::getProjectDir() . '/zz_engine/var/log',
        ];

        foreach ($patchList as $patch) {
            if (!static::canWriteFile($patch)) {
                $return[] = $patch;
            }
        }

        return $return;
    }

    public static function readingFileFailedList(): array
    {
        $return = [];
        $patchList = [
            FilePath::getProjectDir() . '/asset/main.js',
            FilePath::getProjectDir() . '/static/cache/.gitkeep',
            FilePath::getProjectDir() . '/static/category/.gitkeep',
            FilePath::getProjectDir() . '/static/listing/.gitkeep',
            FilePath::getProjectDir() . '/static/logo/.gitkeep',
            FilePath::getProjectDir() . '/static/resized/.gitkeep',
            FilePath::getProjectDir() . '/static/system/logo_default.png',
            FilePath::getProjectDir() . '/static/tmp/file_upload/.gitkeep',
            FilePath::getProjectDir() . '/static/.htaccess',
            FilePath::getProjectDir() . '/static/index.php',
            FilePath::getProjectDir() . '/static/zzzz_2max_io_classified_ads_static_root.txt',
            FilePath::getProjectDir() . '/index.php',
            FilePath::getProjectDir() . '/zz_engine/.htaccess',
            FilePath::getProjectDir() . '/zz_engine/src/Kernel.php',
            FilePath::getProjectDir() . '/zz_engine/var/.gitkeep',
            FilePath::getProjectDir() . '/zz_engine/var/log/.gitkeep',
            FilePath::getProjectDir() . '/zz_engine/src/Controller/Pub/IndexController.php',
        ];

        foreach ($patchList as $patch) {
            $content = @\file_get_contents($patch);
            if (false === $content) {
                $return[] = $patch;
            }
        }

        return $return;
    }

    public static function canWriteFile(string $path): bool
    {
        try {
            $testFilePath = $path . '/' . 'test_safe_to_delete.txt';
            $result = @\file_put_contents($testFilePath, 'test, can safely delete');
            $deleteResult = @\unlink($testFilePath);

            if (false === $result || $deleteResult === false) {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        } finally {
            if (\file_exists($testFilePath)) {
                @\unlink($testFilePath);
            }
        }

        return true;
    }

    public static function canCreateDir(string $path): bool
    {
        try {
            $result = @\mkdir($path);
            $deleteResult = @\rmdir($path);

            if (false === $result || $deleteResult === false) {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        } finally {
            if (\file_exists($path)) {
                @\rmdir($path);
            }
        }

        return true;
    }
}
