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
            // returning raw unsafe html:
            new TwigFunction('setOptionSelectedAttr', [TwigForm::class, 'setOptionSelectedAttr'], ['is_safe' => ['html']]),

            // returning safe HTML:
            new TwigFunction('settings', [TwigSettings::class, 'settings']),
            new TwigFunction('environmentCssClass', [TwigEnvironment::class, 'environmentCssClass']),
            new TwigFunction('isCurrentUserListing', [TwigUser::class, 'isCurrentUserListing']),
            new TwigFunction('displayAsExpired', [TwigUser::class, 'displayAsExpired']),
            new TwigFunction('userOrAdmin', [TwigUser::class, 'userOrAdmin']),
            new TwigFunction('isAdminInPublic', [TwigUser::class, 'isAdminInPublic']),
            new TwigFunction('getListingStatus', [TwigListingStatus::class, 'getListingStatus']),
            new TwigFunction('getListingStatusClass', [TwigListingStatus::class, 'getListingStatusClass']),
            new TwigFunction('listingToActivateForAdmin', [TwigListing::class, 'listingToActivateForAdmin']),
            new TwigFunction('listingToRejectForAdmin', [TwigListing::class, 'listingToRejectForAdmin']),
            new TwigFunction('plusPrefixForPositiveNumber', [TwigNoDependencies::class, 'plusPrefixForPositiveNumber']),
            new TwigFunction('categoryToAdvertZoneId', [TwigAdvert::class, 'categoryToAdvertZoneId']),
        ];
    }

    public function getFilters(): array
    {
        return [
            // returning raw unsafe html:
            new TwigFilter('processDataForJs', [DataForJs::class, 'processDataForJs'], ['is_safe' => ['html']]),
            new TwigFilter('linkify', [TwigNoDependencies::class, 'linkify'], ['is_safe' => ['html']]),
            new TwigFilter('linkifyDoFollow', [TwigNoDependencies::class, 'linkifyDoFollow'], ['is_safe' => ['html']]),

            // returning safe HTML:
            new TwigFilter('boolText', [TwigNoDependencies::class, 'boolText']),
            new TwigFilter('displayTextWarning', [TwigNoDependencies::class, 'displayTextWarning']),
            new TwigFilter('isExpired', [TwigNoDependencies::class, 'isExpired']),
            new TwigFilter('boolGreenRedClass', [TwigNoDependencies::class, 'boolGreenRedClass']),
            new TwigFilter('defaultTrans', [TwigTranslator::class, 'defaultTrans']),
            new TwigFilter('phone', [TwigPhone::class, 'phone']),
            new TwigFilter('displayUserName', [TwigUser::class, 'displayUserName']),
            new TwigFilter('money', [TwigSettings::class, 'money']),
            new TwigFilter('moneyPrecise', [TwigSettings::class, 'moneyPrecise']),
            new TwigFilter('thousandsSeparate', [TwigSettings::class, 'thousandsSeparate']),
        ];
    }
}
