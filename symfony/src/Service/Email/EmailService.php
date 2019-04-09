<?php

declare(strict_types=1);

namespace App\Service\Email;

use App\Entity\User;
use App\System\EnvironmentService;
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

    public function __construct(\Swift_Mailer $mailer, Twig $twig, EnvironmentService $environmentService)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->environmentService = $environmentService;
    }

    public function sendRegisterEmail(User $user): void
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom($this->environmentService->getMailerFromEmailAddress())
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render(
                    'email/registration.html.twig',
                    ['plainPassword' => $user->getPlainPassword()]
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
