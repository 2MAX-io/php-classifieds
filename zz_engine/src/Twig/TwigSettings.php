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

    public function thousandsSeparate(int $value): string
    {
        return \number_format(
            $value,
            0,
            $this->settingsService->getSettingsDto()->getDecimalSeparator(),
            $this->settingsService->getSettingsDto()->getThousandSeparator(),
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
            $this->settingsService->getSettingsDto()->getDecimalSeparator(),
            $this->settingsService->getSettingsDto()->getThousandSeparator(),
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
            $this->settingsService->getSettingsDto()->getDecimalSeparator(),
            $this->settingsService->getSettingsDto()->getThousandSeparator(),
        );
    }
}
