<?php

declare(strict_types=1);

namespace App\System\Filesystem;

use App\Helper\FilePath;
use Symfony\Component\Finder\Finder;

class FilesystemChecker
{
    public static function notWritableFileList(): array
    {
        $return = [];

        $finder = new Finder();
        $finder->in(FilePath::getProjectDir());
        $finder->exclude([
            'static',
            'symfony/docker/mysql/data/',
        ]);

        $files = $finder->files()->getIterator();
        $i = 0;
        foreach ($files as $file) {
            ++$i;
            if ($i > 1000) {
                break;
            }

            if (!is_writable($file->getPath())) {
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
            FilePath::getProjectDir() . '/static/cache/listing/test',
            FilePath::getProjectDir() . '/static/listing/test',
            FilePath::getProjectDir() . '/static/resized/test',
            FilePath::getProjectDir() . '/symfony/var/cache/prod_test',
            FilePath::getProjectDir() . '/symfony/var/cache/prod/test',
            FilePath::getProjectDir() . '/symfony/var/log/test',
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
            FilePath::getProjectDir() . '/symfony/var/cache',
            FilePath::getProjectDir() . '/symfony/var/cache/upgrade',
            FilePath::getProjectDir() . '/symfony/var/cache/prod',
            FilePath::getProjectDir() . '/symfony/var/log',
        ];

        foreach ($patchList as $patch) {
            if (!FilesystemChecker::canWriteFile($patch)) {
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
