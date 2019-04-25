<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\Token;
use App\Entity\User;
use App\Service\Email\EmailService;
use App\Service\System\Token\TokenService;
use Carbon\Carbon;
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

    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(EntityManagerInterface $em, EmailService $emailService, TokenService $tokenService)
    {
        $this->em = $em;
        $this->emailService = $emailService;
        $this->tokenService = $tokenService;
    }

    public function sendConfirmation(User $user, string $newEmail)
    {
        $token = $this->tokenService->createToken($newEmail, Token::USER_EMAIL_CHANGE_TYPE, Carbon::now()->add('day', 7));
        $this->emailService->sendEmailChangeConfirmationToPreviousEmail($user, $newEmail, $token);
        $this->emailService->sendEmailChangeNotificationToNewEmail($user, $newEmail, $token);
    }

    public function changeEmail(User $user, string $newEmail)
    {
        $user->setEmail($newEmail);

        $this->em->persist($user);
    }
}
