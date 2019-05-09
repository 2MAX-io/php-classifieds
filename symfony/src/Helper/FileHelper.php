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
        return in_array(
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

        return $generator->generate(pathinfo($filename, PATHINFO_FILENAME));
    }

    public static function throwExceptionIfUnsafeExtension(UploadedFile $uploadedFile): void
    {
        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
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
                'js',
                'css',
                'exe',
                'com',
                'bat',
                'sh',
                'cgi',
            ]
        )) {
            throw new UserVisibleMessageException(
                "file extension $fileExtension is not allowed"
            );
        }
    }
}
