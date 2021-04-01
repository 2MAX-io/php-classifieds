<?php

declare(strict_types=1);

namespace App\Twig;

use App\Helper\DateHelper;
use App\Helper\StringHelper;
use Misd\Linkify\Linkify;
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
        return $date <= DateHelper::create();
    }

    /**
     * @param mixed $value
     */
    public function boolText($value): string
    {
        if (true === $value) {
            return 'trans.Yes';
        }

        if (false === $value) {
            return 'trans.No';
        }

        if ('0' === $value || '' === $value || 'false' === $value || 'null' === $value) {
            return 'trans.No';
        }

        if (StringHelper::emptyTrim($value)) {
            return 'trans.No';
        }

        if ('1' === $value || 1 === $value || 'true' === $value) {
            return 'trans.Yes';
        }

        if ($value) {
            return 'trans.Yes';
        }

        return 'trans.No';
    }

    public function plusPrefixForPositiveNumber(float $number): string
    {
        if ($number > 0) {
            return '+';
        }

        return '';
    }

    public function linkify(?string $text): ?string
    {
        $linkify = new Linkify();

        return $linkify->process($text, ['attr' => ['rel' => 'nofollow']]);
    }

    public function linkifyDoFollow(?string $text): ?string
    {
        $linkify = new Linkify();

        return $linkify->process($text, ['attr' => []]);
    }

    /**
     * @param array<mixed>|null $array
     *
     * @return array<mixed>|null
     */
    public function unique(?array $array): ?array
    {
        if (null === $array) {
            return null;
        }

        return \array_unique($array);
    }
}
