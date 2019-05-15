<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\Str;
use Twig\Extension\RuntimeExtensionInterface;

class TwigNoDependencies implements RuntimeExtensionInterface
{
    public function displayTextWarning(bool $bool): string
    {
        return $bool ? 'text-warning-color' : '';
    }

    public function boolGreenRedClass(bool $bool): string
    {
        return $bool ? 'text-success' : 'text-danger';
    }

    public function isExpired(\DateTime $date): bool
    {
        return $date <= new \DateTime();
    }

    public function boolText($value): string
    {
        if ($value === true) {
            return 'trans.Yes';
        }

        if ($value === false) {
            return 'trans.No';
        }

        if ($value === '0' || $value === '' || $value === 'false' || $value === 'null') {
            return 'trans.No';
        }

        if (Str::emptyTrim($value)) {
            return 'trans.No';
        }

        if ($value === '1' || $value === 1 || $value === 'true') {
            return 'trans.Yes';
        }

        if ($value) {
            return 'trans.Yes';
        }

        return 'trans.No';
    }

    public function moneyAsFloat(int $value): float
    {
        return \round($value / 100, 2);
    }

    public function thousandsSeparate(int $value): string
    {
        return number_format($value, 0, ',', ' ');
    }

    public function money(float $money): string
    {
        if ($money < 40) {
            return (string) round($money, 2);
        }

        return number_format($money, 0, ',', ' ');
    }

    public function prefixWithPlusPositive(float $number): string
    {
        if ($number > 0) {
            return '+' . $number;
        }

        return (string) $number;
    }
}
