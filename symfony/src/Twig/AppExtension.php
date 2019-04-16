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
            new TwigFunction('lowSecurityCheckIsAdminInPublic', [AppRuntime::class, 'lowSecurityCheckIsAdminInPublic']),
            new TwigFunction('isCurrentUserListing', [AppRuntime::class, 'isCurrentUserListing']),
        ];
    }
}
