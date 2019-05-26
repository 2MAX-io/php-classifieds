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

        $files = $finder->files()->getIterator();
        $i = 0;
        foreach ($files as $file) {
            ++$i;
            if ($i > 1000) {
                break;
            }

            if (!is_writable($file->getPath()) || !\is_readable($file->getPath())) {
                $return[] = $file->getPath();
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

        $files = $finder->directories()->getIterator();
        $i = 0;
        foreach ($files as $file) {
            ++$i;
            if ($i > 1000) {
                break;
            }

            if (!is_writable($file->getPath()) || !\is_readable($file->getPath())) {
                $return[] = $file->getPath();
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
            FilePath::getProjectDir() . '/static/system/logo_default.png',
            FilePath::getProjectDir() . '/index.php',
            FilePath::getProjectDir() . '/asset/main.js',
            FilePath::getProjectDir() . '/static/cache/.gitkeep',
            FilePath::getProjectDir() . '/static/category/.gitkeep',
            FilePath::getProjectDir() . '/static/listing/.gitkeep',
            FilePath::getProjectDir() . '/static/logo/.gitkeep',
            FilePath::getProjectDir() . '/static/resized/.gitkeep',
            FilePath::getProjectDir() . '/static/.htaccess',
            FilePath::getProjectDir() . '/zz_engine/.htaccess',
            FilePath::getProjectDir() . '/zz_engine/var/.gitkeep',
        ];

        foreach ($patchList as $patch) {
            $content = \file_get_contents($patch);
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
            $result = \file_put_contents($testFilePath, 'test, can safely delete');

            $deleteResult = \unlink($testFilePath);

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
            $result = \mkdir($path);
            $deleteResult = \rmdir($path);

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
