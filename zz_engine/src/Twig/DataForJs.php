<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\ParamEnum;
use App\Helper\JsonHelper;
use App\Service\Setting\SettingsDto;
use Twig\Extension\RuntimeExtensionInterface;

class DataForJs implements RuntimeExtensionInterface
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    /**
     * @param array<string,array<string,string>> $dataForJs
     */
    public function processDataForJs(array $dataForJs): string
    {
        $dataForJs[ParamEnum::LANGUAGE_ISO] = $this->settingsDto->getLanguageIso();
        $dataForJs[ParamEnum::COUNTRY_ISO] = $this->settingsDto->getCountryIso();
        $dataForJs[ParamEnum::THOUSAND_SEPARATOR] = $this->settingsDto->getThousandSeparator();
        $dataForJs[ParamEnum::NUMERAL_DECIMAL_MARK] = $this->settingsDto->getDecimalSeparator();

        return \base64_encode(JsonHelper::toString($dataForJs));
    }
}
