<?php

declare(strict_types=1);

namespace App\Helper;

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;

class SlugHelper
{
    public static function getSlug(string $sting, string $delimiter = '-'): string
    {
        $generator = new SlugGenerator(
            (new SlugOptions())
                ->setValidChars('a-z0-9')
                ->setDelimiter($delimiter)
        );

        $slug = $generator->generate(\trim($sting));
        if ('' === $slug) {
            throw new \RuntimeException('could not generate not empty slug');
        }

        return $slug;
    }

    public static function lowercaseWithoutSpaces(?string $value): ?string
    {
        if (null === $value) {
            return $value;
        }

        $value = \mb_strtolower($value);

        return StringHelper::replaceMultipleToSingle($value, [' '], '-');
    }
}
