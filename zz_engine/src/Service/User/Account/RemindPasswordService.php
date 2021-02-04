<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Entity\User;
use App\Exception\UserVisibleException;
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
     * @var UserAccountEmailService
     */
    private $userAccountEmailService;
    /**
     * @var PasswordGenerateService
     */
    private $passwordGenerateService;

    public function __construct(
        EntityManagerInterface $em,
        EncodePasswordService $encodePasswordService,
        TokenService $tokenService,
        PasswordGenerateService $passwordGenerateService,
        UserAccountEmailService $userAccountEmailService
    ) {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
        $this->tokenService = $tokenService;
        $this->userAccountEmailService = $userAccountEmailService;
        $this->passwordGenerateService = $passwordGenerateService;
    }

    public function sendRemindConfirmation(string $email): void
    {
        $newPassword = $this->passwordGenerateService->generatePassword();
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserVisibleException('trans.email address not found');
        }
        $hashedPassword = $this->encodePasswordService->getEncodedPassword($user, $newPassword);

        $user->setPlainPassword($newPassword);

        $tokenDto = $this->tokenService->getTokenBuilder(
            Token::USER_PASSWORD_REMIND,
            Carbon::now()->add('day', 7)
        );
        $tokenDto->addField(TokenField::USER_ID_FIELD, (string) $user->getId());
        $tokenDto->addField(TokenField::REMINDED_HASHED_PASSWORD, $hashedPassword);

        $this->em->persist($tokenDto->getTokenEntity());

        $this->userAccountEmailService->remindPasswordConfirmation($user, $tokenDto->getTokenEntity()->getTokenString());
    }

    public function setHashedPassword(User $user, string $newPasswordHash): void
    {
        $user->setPassword($newPasswordHash);

        $this->em->persist($user);
    }
}
