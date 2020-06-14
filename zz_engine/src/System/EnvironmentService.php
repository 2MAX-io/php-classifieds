<?php

declare(strict_types=1);

namespace App\System;

use App\Helper\BoolHelper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EnvironmentService
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getTwigDateFormat(): string
    {
        return $this->parameterBag->get('twig_date_format');
    }

    public function getTwigDateFormatShort(): string
    {
        return $this->parameterBag->get('twig_date_format_short');
    }

    public function getAppTimezone(): string
    {
        return $this->parameterBag->get('timezone');
    }

    public function getUpgradeDisabled(): bool
    {
        return $this->parameterBag->get('upgrade_disabled')
            || (isset($_ENV['APP_UPGRADE_DISABLED']) && BoolHelper::isTrue($_ENV['APP_UPGRADE_DISABLED']));
    }

    public function getUpgradeAvailableCheckDisabled(): bool
    {
        return $this->parameterBag->get('upgrade_available_check_disabled')
            || (
                isset($_ENV['APP_UPGRADE_AVAILABLE_CHECK_DISABLED'])
                && BoolHelper::isTrue(
                    $_ENV['APP_UPGRADE_AVAILABLE_CHECK_DISABLED']
                )
            );
    }

    public function getCacheDir(): string
    {
        return $this->parameterBag->get('kernel.cache_dir');
    }

    public function getExternalImageUrlForDevelopment(): ?string
    {
        if (!isset($_ENV['APP_DEV_EXTERNAL_IMAGE_URL'])) {
            return null;
        }

        return $_ENV['APP_DEV_EXTERNAL_IMAGE_URL'];
    }

    public function getEnvironmentCssClass(): string
    {
        return $this->parameterBag->get('environment_css_class');
    }
}
