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
            || isset($_ENV['APP_UPGRADE_DISABLED'])
            && BoolHelper::isTrue($_ENV['APP_UPGRADE_DISABLED']);
    }

    public function getUpgradeAvailableCheckDisabled(): bool
    {
        return $this->parameterBag->get('upgrade_available_check_disabled')
            || isset($_ENV['APP_UPGRADE_AVAILABLE_CHECK_DISABLED'])
            && BoolHelper::isTrue($_ENV['APP_UPGRADE_AVAILABLE_CHECK_DISABLED']);
    }
}
