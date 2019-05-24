<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Setting\SettingsDto;
use App\Service\Setting\SettingsService;
use Twig\Extension\RuntimeExtensionInterface;

class TwigSettings implements RuntimeExtensionInterface
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function settings(): SettingsDto
    {
        return $this->settingsService->getSettingsDto();
    }
}
