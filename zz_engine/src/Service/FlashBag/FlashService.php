<?php

declare(strict_types=1);

namespace App\Service\FlashBag;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashService
{
    public const INFO = 'info_flash';
    public const SUCCESS_ABOVE_FORM = 'success_above_form';
    public const ERROR_ABOVE_FORM = 'error_above_form';

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(FlashBagInterface $flashBag, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->session = $session;
    }

    /**
     * @param string $message #TranslationKey
     */
    public function addFlash(string $type, string $message, array $translationParams = []): void
    {
        $this->session->start();
        $this->flashBag->add($type, $this->translator->trans($message, $translationParams));
    }
}
