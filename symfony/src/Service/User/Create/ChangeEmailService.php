<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use App\Helper\Random;
use App\Service\Email\EmailService;
use Doctrine\ORM\EntityManagerInterface;

class ChangeEmailService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(EntityManagerInterface $em, EmailService $emailService)
    {
        $this->em = $em;
        $this->emailService = $emailService;
    }

    public function sendConfirmation(User $user, string $newEmail)
    {
        $user->setConfirmationToken(Random::string(40));
        $this->emailService->sendEmailChangeConfirmationToPreviousEmail($user, $newEmail);
        $this->emailService->sendEmailChangeNotificationToNewEmail($user, $newEmail);
    }

    public function changeEmail(User $user, string $newEmail)
    {
        $user->setEmail($newEmail);

        $this->em->persist($user);
    }
}
