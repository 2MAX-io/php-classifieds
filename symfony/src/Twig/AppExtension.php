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
            new TwigFunction('lowSecurityCheckIsAdminInPublic', [TwigUser::class, 'lowSecurityCheckIsAdminInPublic']),
            new TwigFunction('isCurrentUserListing', [TwigUser::class, 'isCurrentUserListing']),
            new TwigFunction('getListingStatus', [TwigListingStatus::class, 'getListingStatus']),
            new TwigFunction('getListingStatusClass', [TwigListingStatus::class, 'getListingStatusClass']),
            new TwigFunction('adminShowActivate', [TwigListing::class, 'adminShowActivate']),
            new TwigFunction('adminShowReject', [TwigListing::class, 'adminShowReject']),
            new TwigFunction('displayAsExpired', [TwigUser::class, 'displayAsExpired']),
            new TwigFunction('settings', [TwigSettings::class, 'settings']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('boolText', [TwigNoDependencies::class, 'boolText']),
            new TwigFilter('displayTextWarning', [TwigNoDependencies::class, 'displayTextWarning']),
            new TwigFilter('isExpired', [TwigNoDependencies::class, 'isExpired']),
            new TwigFilter('moneyAsFloat', [TwigNoDependencies::class, 'moneyAsFloat']),
            new TwigFilter('money', [TwigNoDependencies::class, 'money']),
            new TwigFilter('defaultTrans', [TwigTranslator::class, 'defaultTrans']),
        ];
    }
}
