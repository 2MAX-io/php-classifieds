<?php

declare(strict_types=1);

namespace App\Service\FlashBag;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashService
{
    public const INFO = 'INFO';
    public const SUCCESS_ABOVE_FORM = 'SUCCESS_ABOVE_FORM';
    public const ERROR_ABOVE_FORM = 'ERROR_ABOVE_FORM';

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
