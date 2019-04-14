<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\User;
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

    public function __construct(\Swift_Mailer $mailer, Twig $twig, EnvironmentService $environmentService, TranslatorInterface $trans)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->environmentService = $environmentService;
        $this->trans = $trans;
    }

    public function sendRegisterEmail(User $user): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.You have registered account')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->environmentService->getMailerFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/registration.html.twig',
                    ['user' => $user]
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function sendEmailChangeConfirmationToPreviousEmail(User $user, string $newEmail): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Change email confirmation')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->environmentService->getMailerFromName())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/change_email_previous_email_confirmation.html.twig',
                    [
                        'user' => $user,
                        'newEmail' => $newEmail,
                    ]
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }

    public function sendEmailChangeNotificationToNewEmail(User $user, string $newEmail): void
    {
        $message = (new \Swift_Message($this->trans->trans('trans.Change email confirmation')))
            ->setReplyTo($this->environmentService->getMailerReplyToAddress())
            ->setFrom($this->environmentService->getMailerFromEmailAddress(), $this->environmentService->getMailerFromName())
            ->setTo($newEmail)
            ->setBody(
                $this->twig->render(
                    'email/change_email_new_email_notification.html.twig',
                    [
                        'user' => $user,
                        'newEmail' => $newEmail,
                    ]
                ),
                'text/html'
            )
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;
        $this->mailer->send($message);
        $this->mailer->getTransport();
    }
}
