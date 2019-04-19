<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('lowSecurityCheckIsAdminInPublic', [AppRuntime::class, 'lowSecurityCheckIsAdminInPublic']),
            new TwigFunction('isCurrentUserListing', [AppRuntime::class, 'isCurrentUserListing']),
            new TwigFunction('getListingStatus', [TwigListingRuntime::class, 'getListingStatus']),
            new TwigFunction('getListingStatusClass', [TwigListingRuntime::class, 'getListingStatusClass']),
            new TwigFunction('adminShowActivate', [TwigListingRuntime::class, 'adminShowActivate']),
            new TwigFunction('adminShowReject', [TwigListingRuntime::class, 'adminShowReject']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('boolText', [AppRuntime::class, 'boolText']),
            new TwigFilter('displayTextWarning', [TwigListingRuntime::class, 'displayTextWarning']),
            new TwigFilter('isExpired', [TwigListingRuntime::class, 'isExpired']),
        ];
    }
}
