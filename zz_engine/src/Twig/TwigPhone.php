<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Setting\SettingsDto;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Psr\Log\LoggerInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigPhone implements RuntimeExtensionInterface
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PhoneNumberUtil $phoneNumberUtil,
        SettingsDto $settingsDto,
        LoggerInterface $logger
    ) {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->settingsDto = $settingsDto;
        $this->logger = $logger;
    }

    public function phone(string $phoneString): string
    {
        /** @var null|PhoneNumber $phoneNumber */
        $phoneNumber = $this->phoneNumberUtil->parse(
            $phoneString,
            $this->settingsDto->getCountryIso(),
        );
        if (!$phoneNumber) {
            $this->logger->error('could not parse phone number', [
                '$phoneString' => $phoneString,
            ]);

            return $phoneString;
        }

        return $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::NATIONAL);
    }
}
