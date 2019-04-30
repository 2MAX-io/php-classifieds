<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class TwigNoDependencies implements RuntimeExtensionInterface
{
    public function displayTextWarning(bool $bool): string
    {
        return $bool ? 'text-warning-color' : '';
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

        if (empty(trim($value))) {
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

    public function money(float $money): float
    {
        if ($money < 40) {
            return round($money, 2);
        }

        return round($money, 0);
    }
}
