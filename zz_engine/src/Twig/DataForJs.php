<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\ParamEnum;
use App\Helper\JsonHelper;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DataForJs implements RuntimeExtensionInterface
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(SettingsDto $settingsDto, UrlGeneratorInterface $urlGenerator)
    {
        $this->settingsDto = $settingsDto;
        $this->urlGenerator = $urlGenerator;
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
        $dataForJs[ParamEnum::BASE_URL] = $this->urlGenerator->generate(
            'app_public_dir',
            [],
            UrlGeneratorInterface::ABSOLUTE_PATH,
        );

        return \base64_encode(JsonHelper::toString($dataForJs));
    }
}
