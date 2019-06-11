<?php

declare(strict_types=1);

namespace App\Helper;

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;

class SlugHelper
{
    public static function getSlug(string $sting): string
    {
        $generator = new SlugGenerator(
            (new SlugOptions)
                ->setValidChars('a-z0-9')
                ->setDelimiter('-')
        );

        $slug = $generator->generate(\trim($sting));
        if ($slug === '') {
            return 'classified-ads-item';
        }

        return $slug;
    }
}
