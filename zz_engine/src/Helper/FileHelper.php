<?php

declare(strict_types=1);

namespace App\Helper;

use App\Exception\UserVisibleMessageException;
use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\PathUtil\Path;

class FileHelper
{
    public static function isImage(string $path): bool
    {
        return \in_array(
            Path::getExtension($path, true),
            ['jpg', 'png', 'gif', 'jpeg'],
            true
        );
    }

    public static function getFilenameValidCharacters(string $filename): string
    {
        $generator = new SlugGenerator(
            (new SlugOptions)
                ->setValidChars('a-zA-Z0-9')
                ->setDelimiter('_')
        );

        return $generator->generate(\pathinfo($filename, \PATHINFO_FILENAME));
    }

    public static function throwExceptionIfUnsafeExtensionFromUploadedFile(UploadedFile $uploadedFile): void
    {
        static::throwExceptionIfUnsafeExtension($uploadedFile->getClientOriginalExtension());
    }

    public static function throwExceptionIfUnsafeFilename(string $filename): void
    {
        static::throwExceptionIfUnsafeExtension(\pathinfo($filename, \PATHINFO_EXTENSION));
    }

    public static function throwExceptionIfUnsafePath(string $path, string $mustBeInsideDir = null): void
    {
        $mustBeInsideDir = $mustBeInsideDir ?? FilePath::getStaticPath();
        if (Path::getLongestCommonBasePath([Path::canonicalize($path), $mustBeInsideDir]) !== $mustBeInsideDir) {
            throw new UserVisibleMessageException(
                'detected file path change'
            );
        }

        static::throwExceptionIfUnsafeExtension(\pathinfo($path, \PATHINFO_EXTENSION));
    }

    public static function throwExceptionIfUnsafeExtension(string $extension): void
    {
        $fileExtension = \mb_strtolower($extension);
        if (!Arr::inArray(
            $fileExtension,
            [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'swf',
            ]
        )) {
            throw new UserVisibleMessageException(
                "file extension $fileExtension is not allowed"
            );
        }

        if (Arr::inArray(
            $fileExtension,
            [
                'php',
                'php3',
                'php4',
                'php5',
                'php7',
                'php71',
                'php72',
                'php73',
                'php74',
                'php75',
                'phtml',
                'js',
                'css',
                'exe',
                'com',
                'bat',
                'sh',
                'cgi',
                'htaccess',
            ]
        )) {
            throw new UserVisibleMessageException(
                "file extension $fileExtension is not allowed"
            );
        }

        if (\preg_match('~^php\d+~', $fileExtension)) {
            throw new UserVisibleMessageException(
                "file extension $fileExtension is not allowed"
            );
        }
    }
}
