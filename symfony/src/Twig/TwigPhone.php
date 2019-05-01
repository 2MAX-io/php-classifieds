<?php

declare(strict_types=1);

namespace App\Twig;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Twig\Extension\RuntimeExtensionInterface;

class TwigPhone implements RuntimeExtensionInterface
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    public function __construct(PhoneNumberUtil $phoneNumberUtil)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
    }

    public function phone(string $phoneString): string
    {
        $phoneNumber = $this->phoneNumberUtil->parse('+48' . $phoneString); // todo: from settings

        return $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::NATIONAL);
    }
}
