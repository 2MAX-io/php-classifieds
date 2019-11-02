<?php

declare(strict_types=1);

namespace App\Helper;

use App\Exception\UserVisibleException;
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

    public static function throwExceptionIfNotImage(string $path): void
    {
        if (!static::isImage($path)) {
            throw new \UnexpectedValueException('file is not image');
        }
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

    public static function throwExceptionIfPathOutsideDir(string $path, string $mustBeInsideDir = null): void
    {
        $mustBeInsideDir = $mustBeInsideDir ?? FilePath::getStaticPath();
        if (Path::getLongestCommonBasePath([Path::canonicalize($path), $mustBeInsideDir]) !== $mustBeInsideDir) {
            throw new UserVisibleException(
                'detected file path change'
            );
        }

        static::throwExceptionIfUnsafeExtension(\pathinfo($path, \PATHINFO_EXTENSION));
    }

    public static function reducePathLength(string $path, int $maxLength = 255): string
    {
        $dirname = \dirname($path);
        $extension = '.' . \pathinfo($path, \PATHINFO_EXTENSION);
        $usedLength = \strlen(Path::makeRelative($dirname . '/' .$extension, FilePath::getProjectDir()));
        $filenameMaxLength = $maxLength - $usedLength - 1;

        $return = $dirname;
        $return .= '/' . \substr(\pathinfo($path, \PATHINFO_FILENAME), 0, $filenameMaxLength);
        $return .= $extension;

        return $return;
    }

    public static function reduceFilenameLength(string $path, int $maxLength = 100): string
    {
        $extension = '.' . \pathinfo($path, \PATHINFO_EXTENSION);
        $filenameMaxLength = $maxLength - \strlen($extension) - 1;

        $return = \dirname($path);
        $return .= '/' . \substr(\pathinfo($path, \PATHINFO_FILENAME), 0, $filenameMaxLength);
        $return .= $extension;

        return $return;
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
            throw new UserVisibleException(
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
            throw new UserVisibleException(
                "file extension $fileExtension is not allowed"
            );
        }

        if (\preg_match('~^php\d+~', $fileExtension)) {
            throw new UserVisibleException(
                "file extension $fileExtension is not allowed"
            );
        }
    }
}
