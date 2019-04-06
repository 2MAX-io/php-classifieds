<?php

declare(strict_types=1);

namespace App\Service\Setting;

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

    public function getDateFormat(): string
    {
        return $this->parameterBag->get('date_format');
    }

    public function getDateFormatShort(): string
    {
        return $this->parameterBag->get('date_format_short');
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
                && BoolHelper::isTrue($_ENV['APP_UPGRADE_AVAILABLE_CHECK_DISABLED'])
            );
    }

    public function getCacheDir(): string
    {
        return $this->parameterBag->get('kernel.cache_dir');
    }

    public function getExternalImageUrlForDevelopment(): ?string
    {
        return $_ENV['APP_DEV_EXTERNAL_IMAGE_URL'] ?? null;
    }

    public function getEnvironmentCssClass(): string
    {
        return $this->parameterBag->get('environment_css_class');
    }
}
