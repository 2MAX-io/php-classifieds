<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\Token;
use App\Entity\User;
use App\Service\Email\EmailService;
use App\Service\System\Token\TokenService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class ChangePasswordService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EncodePasswordService
     */
    private $encodePasswordService;

    /**
     * @var TokenService
     */
    private $tokenService;
    /**
     * @var EmailService
     */
    private $emailService;

    public function __construct(
        EntityManagerInterface $em,
        EncodePasswordService $encodePasswordService,
        TokenService $tokenService,
        EmailService $emailService
    ) {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
        $this->tokenService = $tokenService;
        $this->emailService = $emailService;
    }

    public function sendConfirmation(User $user, string $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $hashedPassword = $this->encodePasswordService->getEncodedPassword($user, $newPassword);
        unset($newPassword);

        $token = $this->tokenService->createToken($hashedPassword, Token::USER_PASSWORD_CHANGE_TYPE, Carbon::now()->add('day', 7));
        $user->setConfirmationToken($token);
        $this->emailService->changePasswordConfirmation($user);
    }

    public function changePassword(User $user, string $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $this->encodePasswordService->setEncodedPassword($user, $newPassword);

        $this->em->persist($user);
    }

    public function setHashedPassword(User $user, string $newPasswordHash)
    {
        $user->setPassword($newPasswordHash);

        $this->em->persist($user);
    }
}
