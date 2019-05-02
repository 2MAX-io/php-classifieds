<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Setting\SettingsService;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Twig\Extension\RuntimeExtensionInterface;

class TwigPhone implements RuntimeExtensionInterface
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(PhoneNumberUtil $phoneNumberUtil, SettingsService $settingsService)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->settingsService = $settingsService;
    }

    public function phone(string $phoneString): string
    {
        $phoneNumber = $this->phoneNumberUtil->parse(
            $phoneString,
            $this->settingsService->getLanguageTwoLetters()
        );

        return $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::NATIONAL);
    }
}
