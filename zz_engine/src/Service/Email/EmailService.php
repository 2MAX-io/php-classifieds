<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\User;
use App\Service\Setting\SettingsService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailService
{
    /**
     * @var \Swift_Mailer
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

    public function sendRegisterEmail(User $user, string $token): void
    {
        $email = new TemplatedEmail();
        $email->subject($this->trans->trans('trans.Confirm account registration'));
        $email->from(new NamedAddress($this->getEmailFromAddress(), $this->getEmailFromName()));
        $email->to($user->getEmail());
        $email->replyTo($this->getEmailReplyTo());
        $email->htmlTemplate('email/registration.html.twig');
        $email->context([
            'user' => $user,
            'token' => $token,
        ]);
        $this->mailer->send($email);
    }

    public function sendEmailChangeConfirmationToPreviousEmail(User $user, string $newEmail, string $token): void
    {
        $email = new TemplatedEmail();
        $email->subject($this->trans->trans('trans.Confirmation of email address change'));
        $email->from(new NamedAddress($this->getEmailFromAddress(), $this->getEmailFromName()));
        $email->to($user->getEmail());
        $email->replyTo($this->getEmailReplyTo());

        $email->htmlTemplate('email/change_email_previous_email_confirmation.html.twig');
        $email->context([
                'user' => $user,
                'newEmail' => $newEmail,
                'token' => $token,
            ]
        );
        $this->mailer->send($email);
    }

    public function sendEmailChangeNotificationToNewEmail(User $user, string $newEmail, string $token): void
    {
        $email = new TemplatedEmail();
        $email->subject($this->trans->trans('trans.Verification of the correctness of the new email address'));
        $email->from(new NamedAddress($this->getEmailFromAddress(), $this->getEmailFromName()));
        $email->to($user->getEmail());
        $email->replyTo($this->getEmailReplyTo());

        $email->htmlTemplate('email/change_email_new_email_notification.html.twig');
        $email->context([
                'user' => $user,
                'newEmail' => $newEmail,
                'oldEmail' => $user->getEmail(),
                'token' => $token,
            ]
        );
        $this->mailer->send($email);
    }

    public function changePasswordConfirmation(User $user, string $token): void
    {
        $email = new TemplatedEmail();
        $email->subject($this->trans->trans('trans.Password change confirmation'));
        $email->from(new NamedAddress($this->getEmailFromAddress(), $this->getEmailFromName()));
        $email->to($user->getEmail());
        $email->replyTo($this->getEmailReplyTo());

        $email->htmlTemplate('email/change_password_confirmation.html.twig');
        $email->context([
                'user' => $user,
                'token' => $token,
            ]
        );
        $this->mailer->send($email);
    }

    public function remindPasswordConfirmation(User $user, string $token): void
    {
        $email = new TemplatedEmail();
        $email->subject($this->trans->trans('trans.Password remind confirmation'));
        $email->from(new NamedAddress($this->getEmailFromAddress(), $this->getEmailFromName()));
        $email->to($user->getEmail());
        $email->replyTo($this->getEmailReplyTo());

        $email->htmlTemplate('email/remind_password_confirmation.html.twig');
        $email->context([
                'user' => $user,
                'token' => $token,
            ]
        );
        $this->mailer->send($email);
    }

    private function getEmailFromName(): string
    {
        $emailFromName = $this->settingsService->getSettingsDto()->getEmailFromName();

        if (null === $emailFromName) {
            return $this->trans->trans('trans.Classifieds');
        }

        return $emailFromName;
    }

    private function getEmailFromAddress(): ?string
    {
        return $this->settingsService->getSettingsDto()->getEmailFromAddress();
    }

    private function getEmailReplyTo(): ?string
    {
        if (!empty($this->settingsService->getSettingsDto()->getEmailReplyTo())) {
            return $this->settingsService->getSettingsDto()->getEmailReplyTo();
        }
        
        return $this->settingsService->getSettingsDto()->getEmailFromAddress();
    }
}
