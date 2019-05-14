<?php

declare(strict_types=1);

namespace App\Helper;

use App\Service\Setting\SettingsDto;
use App\Service\Setting\SettingsService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ContainerHelper
{
    public static function getContainer(): ?ContainerInterface
    {
        if (!isset($GLOBALS['kernel'])) {
            return null;
        }

        if (!$GLOBALS['kernel'] instanceof KernelInterface) {
            return null;
        }

        return $GLOBALS['kernel']->getContainer();
    }

    public static function getSettings(): SettingsDto
    {
        return static::getContainer()->get(SettingsService::class)->getSettingsDto();
    }
}
