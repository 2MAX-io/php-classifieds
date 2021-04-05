<?php

declare(strict_types=1);

namespace App\Service\System\Email;

use App\Service\Setting\SettingsDto;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailService
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(
        SettingsDto $settingsDto,
        TranslatorInterface $trans
    ) {
        $this->trans = $trans;
        $this->settingsDto = $settingsDto;
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
        $emailFromName = $this->settingsDto->getEmailFromName();

        return $emailFromName ?? $this->trans->trans('trans.Classifieds');
    }

    public function getEmailFromAddress(): ?string
    {
        return $this->settingsDto->getEmailFromAddress();
    }

    public function getEmailReplyTo(): ?string
    {
        if (!empty($this->settingsDto->getEmailReplyTo())) {
            return $this->settingsDto->getEmailReplyTo();
        }

        return $this->settingsDto->getEmailFromAddress();
    }
}
