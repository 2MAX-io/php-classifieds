<?php

declare(strict_types=1);

namespace App\System\Filesystem;

use App\Helper\FilePath;
use Symfony\Component\Finder\Finder;

class FilesystemChecker
{
    public static function incorrectFilePermissionList(): array
    {
        $return = [];

        $finder = new Finder();
        $finder->in(FilePath::getProjectDir());
        $finder->exclude([
            'static',
            'zz_engine/docker/mysql/data/',
        ]);

        $fileIterator = $finder->files()->getIterator();
        $i = 0;
        $fileIterator->rewind();
        while ($fileIterator->valid()) {
            try {

                $fileIterator->next();
                $file = $fileIterator->current();

                if (!is_writable($file->getPath()) || !\is_readable($file->getPath()) || false === @\file_get_contents($file->getPath(), false, null, 0, 1)) {
                    ++$i;
                    if ($i > 1000) {
                        break;
                    }
                    $return[] = $file->getPath();
                }
            } catch (\Throwable $e) {
                $return[] = $e->getMessage();
            }

        }

        return $return;
    }

    public static function incorrectDirPermissionList(): array
    {
        $return = [];

        $finder = new Finder();
        $finder->in(FilePath::getProjectDir());
        $finder->exclude([
            'static',
            'zz_engine/docker/mysql/data/',
        ]);

        $dirIterator = $finder->directories()->getIterator();

        $i = 0;
        $dirIterator->rewind();
        while ($dirIterator->valid()) {
            try {
                $dirIterator->next();
                $dir = $dirIterator->current();

                if (!is_writable($dir->getPath()) || !\is_readable($dir->getPath())) {
                    ++$i;
                    if ($i > 1000) {
                        break;
                    }
                    $return[] = $dir->getPath();
                }
            } catch (\Throwable $e) {
                $return[] = $e->getMessage();
            }

        }

        return $return;
    }

    public static function creatingDirFailedList(): array
    {
        $return = [];
        $patchList = [
            FilePath::getProjectDir() . '/static/cache/test',
            FilePath::getProjectDir() . '/static/listing/test',
            FilePath::getProjectDir() . '/static/resized/test',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod_test',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod/test',
            FilePath::getProjectDir() . '/zz_engine/var/log/test',
        ];

        foreach ($patchList as $patch) {
            if (!FilesystemChecker::canCreateDir($patch)) {
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
            FilePath::getProjectDir() . '/zz_engine/var/cache',
            FilePath::getProjectDir() . '/zz_engine/var/cache/upgrade',
            FilePath::getProjectDir() . '/zz_engine/var/cache/prod',
            FilePath::getProjectDir() . '/zz_engine/var/log',
        ];

        foreach ($patchList as $patch) {
            if (!FilesystemChecker::canWriteFile($patch)) {
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
            FilePath::getProjectDir() . '/static/.htaccess',
            FilePath::getProjectDir() . '/static/index.php',
            FilePath::getProjectDir() . '/static/zzzz_2max_io_classified_ads_static_root.txt',
            FilePath::getProjectDir() . '/index.php',
            FilePath::getProjectDir() . '/zz_engine/.htaccess',
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
                \unlink($testFilePath);
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
                \rmdir($path);
            }
        }

        return true;
    }
}
