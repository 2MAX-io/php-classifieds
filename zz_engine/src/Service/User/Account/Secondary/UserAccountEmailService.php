<?php

declare(strict_types=1);

namespace App\Service\User\Account\Secondary;

use App\Entity\User;
use App\Service\System\Email\EmailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAccountEmailService
{
    /**
     * @var EmailService
     */
    private $emailService;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(EmailService $emailService, MailerInterface $mailer, TranslatorInterface $trans)
    {
        $this->emailService = $emailService;
        $this->mailer = $mailer;
        $this->trans = $trans;
    }

    public function sendRegisterEmail(User $user, string $token): void
    {
        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Confirm account registration'));
        $email->to($user->getEmail());
        $email->htmlTemplate('email/account/registration.html.twig');
        $email->textTemplate('email/account/registration.txt.twig');
        $email->context([
            'user' => $user,
            'plainPassword' => $user->getPlainPassword(),
            'token' => $token,
        ]);
        $this->mailer->send($email);
    }

    public function remindPasswordConfirmation(User $user, string $token): void
    {
        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Password remind confirmation'));
        $email->to($user->getEmail());

        $email->htmlTemplate('email/account/remind_password_confirmation.html.twig');
        $email->textTemplate('email/account/remind_password_confirmation.txt.twig');
        $email->context([
            'user' => $user,
            'token' => $token,
            'plainPassword' => $user->getPlainPassword(),
        ]);
        $this->mailer->send($email);
    }

    public function sendEmailChangeConfirmationToPreviousEmail(User $user, string $newEmail, string $token): void
    {
        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Confirmation of email address change'));
        $email->to($user->getEmail());

        $email->htmlTemplate('email/account/change_email_previous_email_confirmation.html.twig');
        $email->textTemplate('email/account/change_email_previous_email_confirmation.txt.twig');
        $email->context([
            'user' => $user,
            'newEmail' => $newEmail,
            'token' => $token,
        ]);
        $this->mailer->send($email);
    }

    public function sendEmailChangeNotificationToNewEmail(User $user, string $newEmail, string $token): void
    {
        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Verification of the correctness of the new email address'));
        $email->to($newEmail);

        $email->htmlTemplate('email/account/change_email_new_email_notification.html.twig');
        $email->textTemplate('email/account/change_email_new_email_notification.txt.twig');
        $email->context([
            'user' => $user,
            'newEmail' => $newEmail,
            'oldEmail' => $user->getEmail(),
            'token' => $token,
        ]);
        $this->mailer->send($email);
    }

    public function changePasswordConfirmation(User $user, string $token): void
    {
        $email = $this->emailService->getTemplatedEmail();
        $email->subject($this->trans->trans('trans.Password change confirmation'));
        $email->to($user->getEmail());

        $email->htmlTemplate('email/account/change_password_confirmation.html.twig');
        $email->textTemplate('email/account/change_password_confirmation.txt.twig');
        $email->context([
            'user' => $user,
            'token' => $token,
        ]);
        $this->mailer->send($email);
    }
}
