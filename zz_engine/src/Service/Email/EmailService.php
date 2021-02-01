<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Service\Setting\SettingsService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        MailerInterface $mailer,
        SettingsService $settingsService,
        TranslatorInterface $trans
    ) {
        $this->mailer = $mailer;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
    }

    public function getTemplatedEmail(): TemplatedEmail
    {
        $email = new TemplatedEmail();
        $email->from(new Address(
            $this->getEmailFromAddress(),
            $this->getEmailFromName(),
        ));
        $email->replyTo($this->getEmailReplyTo());

        return $email;
    }

    public function getEmailFromName(): string
    {
        $emailFromName = $this->settingsService->getSettingsDto()->getEmailFromName();

        if (null === $emailFromName) {
            return $this->trans->trans('trans.Classifieds');
        }

        return $emailFromName;
    }

    public function getEmailFromAddress(): ?string
    {
        return $this->settingsService->getSettingsDto()->getEmailFromAddress();
    }

    public function getEmailReplyTo(): ?string
    {
        if (!empty($this->settingsService->getSettingsDto()->getEmailReplyTo())) {
            return $this->settingsService->getSettingsDto()->getEmailReplyTo();
        }
        
        return $this->settingsService->getSettingsDto()->getEmailFromAddress();
    }
}
