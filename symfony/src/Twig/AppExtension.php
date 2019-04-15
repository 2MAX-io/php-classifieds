<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('isAdmin', [AppRuntime::class, 'isAdmin']),
            new TwigFunction('isCurrentUserListing', [AppRuntime::class, 'isCurrentUserListing']),
        ];
    }
}
