<?php

declare(strict_types=1);

namespace App\Service\System\License;

use App\Service\Setting\SettingsService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LicenseService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(UrlGeneratorInterface $urlGenerator, SettingsService $settingsService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->settingsService = $settingsService;
    }

    public function getCurrentUrlOfLicense(): string
    {
        return $this->urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getLicenseText(): string
    {
        return $this->settingsService->getSettingsDtoWithoutCache()->getLicense() ?? '';
    }
}
