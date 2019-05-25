<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlHelper
{
    public static function getBaseAbsoluteUrl(UrlGeneratorInterface $urlGenerator): string
    {
        return \rtrim($urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL), '/');
    }
}
