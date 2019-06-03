<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class TwigTranslator implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function defaultTrans($value, string $default = ''): string
    {
        if (\twig_test_empty($value)) {
            return $this->translator->trans($default);
        }

        return $value;
    }
}
