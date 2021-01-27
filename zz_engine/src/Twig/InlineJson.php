<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\Json;
use App\Service\Setting\SettingsService;
use App\System\Localization\AppNumberFormatter;
use Twig\Extension\RuntimeExtensionInterface;

class InlineJson implements RuntimeExtensionInterface
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function inlineJson(array $array): string
    {
        $array['cleaveConfig']['delimiter'] = AppNumberFormatter::getThousandSeparator();
        $array['cleaveConfig']['numeralDecimalMark'] = AppNumberFormatter::getDecimalSeparator();
        $array['cleaveConfig']['languageTwoLetters'] = $this->settingsService->getLanguageTwoLetters();

        return Json::toString($array);
    }
}
