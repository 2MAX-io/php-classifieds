<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\User;
use App\Service\Setting\SettingsService;
use App\System\EnvironmentService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as Twig;

class EmailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        \Swift_Mailer $mailer,
        Twig $twig,
        EnvironmentService $environmentService,
        SettingsService $settingsService,
        TranslatorInterface $trans
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->environmentService = $environmentService;
        $this->trans = $trans;
        $this->settingsService = $settingsService;
    }

    public function sendRegisterEmail(User $user, string $token): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Confirm account registration')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->getEmailFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/registration.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function sendEmailChangeConfirmationToPreviousEmail(User $user, string $newEmail, string $token): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Confirmation of email address change')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->getEmailFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/change_email_previous_email_confirmation.html.twig',
                    [
                        'user' => $user,
                        'newEmail' => $newEmail,
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function sendEmailChangeNotificationToNewEmail(User $user, string $newEmail, string $token): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Confirmation of email address change')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->getEmailFromName())
            ->setTo($newEmail)
            ->setBody(
                $this->twig->render(
                    'email/change_email_new_email_notification.html.twig',
                    [
                        'user' => $user,
                        'newEmail' => $newEmail,
                        'oldEmail' => $user->getEmail(),
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function changePasswordConfirmation(User $user, string $token): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Change password confirmation')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->getEmailFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/change_password_confirmation.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function remindPasswordConfirmation(User $user, string $token): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Remind password confirmation')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->getEmailFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/remind_password_confirmation.html.twig',
                    [
                        'user' => $user,
                        'token' => $token,
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    private function getEmailFromName(): string
    {
        $emailFromName = $this->settingsService->getSettingsDto()->getEmailFromName();

        if (null === $emailFromName) {
            return $this->trans->trans('trans.Classifieds');
        }

        return $emailFromName;
    }
}
