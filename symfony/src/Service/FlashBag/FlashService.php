<?php

declare(strict_types=1);

namespace App\Service\FlashBag;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashService
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    /**
     * @param string $message #TranslationKey
     */
    public function addFlash(string $type, string $message, array $translationParams = []): void
    {
        $this->flashBag->add($type, $this->translator->trans($message, $translationParams));
    }
}
