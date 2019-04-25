<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Entity\User;
use App\Service\Email\EmailService;
use App\Service\System\Token\TokenService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class RemindPasswordService
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
    /**
     * @var PasswordGenerateService
     */
    private $passwordGenerateService;

    public function __construct(
        EntityManagerInterface $em,
        EncodePasswordService $encodePasswordService,
        TokenService $tokenService,
        PasswordGenerateService $passwordGenerateService,
        EmailService $emailService
    ) {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
        $this->tokenService = $tokenService;
        $this->emailService = $emailService;
        $this->passwordGenerateService = $passwordGenerateService;
    }

    public function sendRemindConfirmation(string $email)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        $newPassword = $this->passwordGenerateService->generatePassword();
        $user->setPlainPassword($newPassword);
        $hashedPassword = $this->encodePasswordService->getEncodedPassword($user, $newPassword);

        $tokenDto = $this->tokenService->getTokenBuilder(
            Token::USER_PASSWORD_REMIND,
            Carbon::now()->add('day', 7)
        );
        $tokenDto->addField(TokenField::USER_ID_FIELD, (string) $user->getId());
        $tokenDto->addField(TokenField::REMINDED_HASHED_PASSWORD, (string) $hashedPassword);

        $this->em->persist($tokenDto->getToken());

        $this->emailService->remindPasswordConfirmation($user, $tokenDto->getToken()->getTokenString());
    }

    public function setHashedPassword(User $user, string $newPasswordHash)
    {
        $user->setPassword($newPasswordHash);

        $this->em->persist($user);
    }
}
