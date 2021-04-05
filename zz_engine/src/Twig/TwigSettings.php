<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Setting\SettingsDto;
use Twig\Extension\RuntimeExtensionInterface;

class TwigSettings implements RuntimeExtensionInterface
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    public function settings(): SettingsDto
    {
        return $this->settingsDto;
    }

    public function thousandsSeparate(int $value): string
    {
        return \number_format(
            $value,
            0,
            $this->settingsDto->getDecimalSeparator(),
            $this->settingsDto->getThousandSeparator(),
        );
    }

    public function money(float $money, int $decimals = 2): string
    {
        if (\abs($money - (int) $money) < 1 / 10 ** ($decimals + 1)) {
            $decimals = 0;
        }

        return \number_format(
            $money,
            $decimals,
            $this->settingsDto->getDecimalSeparator(),
            $this->settingsDto->getThousandSeparator(),
        );
    }

    public function moneyPrecise(?int $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return \number_format(
            \round($value / 100, 2),
            2,
            $this->settingsDto->getDecimalSeparator(),
            $this->settingsDto->getThousandSeparator(),
        );
    }
}
