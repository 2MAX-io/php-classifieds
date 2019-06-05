<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('lowSecurityCheckIsAdminInPublic', [TwigUser::class, 'lowSecurityCheckIsAdminInPublic']),
            new TwigFunction('isCurrentUserListing', [TwigUser::class, 'isCurrentUserListing']),
            new TwigFunction('getListingStatus', [TwigListingStatus::class, 'getListingStatus']),
            new TwigFunction('getListingStatusClass', [TwigListingStatus::class, 'getListingStatusClass']),
            new TwigFunction('adminShowActivate', [TwigListing::class, 'adminShowActivate']),
            new TwigFunction('adminShowReject', [TwigListing::class, 'adminShowReject']),
            new TwigFunction('displayAsExpired', [TwigUser::class, 'displayAsExpired']),
            new TwigFunction('displayAsExpiredForEveryone', [TwigUser::class, 'displayAsExpiredForEveryone']),
            new TwigFunction('userOrAdmin', [TwigUser::class, 'userOrAdmin']),
            new TwigFunction('settings', [TwigSettings::class, 'settings']),
            new TwigFunction('getCleaveConfig', [TwigNoDependencies::class, 'getCleaveConfig']),
            new TwigFunction('optionAttr', [TwigForm::class, 'optionAttr'], ['is_safe' => ['html']]),
            new TwigFunction('returnPlusIfPositive', [TwigNoDependencies::class, 'returnPlusIfPositive']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('boolText', [TwigNoDependencies::class, 'boolText']),
            new TwigFilter('displayTextWarning', [TwigNoDependencies::class, 'displayTextWarning']),
            new TwigFilter('isExpired', [TwigNoDependencies::class, 'isExpired']),
            new TwigFilter('moneyPrecise', [TwigNoDependencies::class, 'moneyPrecise']),
            new TwigFilter('money', [TwigNoDependencies::class, 'money']),
            new TwigFilter('defaultTrans', [TwigTranslator::class, 'defaultTrans']),
            new TwigFilter('phone', [TwigPhone::class, 'phone']),
            new TwigFilter('thousandsSeparate', [TwigNoDependencies::class, 'thousandsSeparate']),
            new TwigFilter('boolGreenRedClass', [TwigNoDependencies::class, 'boolGreenRedClass']),
        ];
    }
}
