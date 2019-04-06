<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Helper\DateHelper;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\Secondary\EncodePasswordService;
use App\Service\User\Account\Secondary\UserAccountEmailService;
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
     * @var UserAccountEmailService
     */
    private $userAccountEmailService;

    public function __construct(
        UserAccountEmailService $userAccountEmailService,
        EncodePasswordService $encodePasswordService,
        TokenService $tokenService,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
        $this->tokenService = $tokenService;
        $this->userAccountEmailService = $userAccountEmailService;
    }

    public function sendConfirmation(User $user, string $newPassword): void
    {
        $hashedPassword = $this->encodePasswordService->getEncodedPassword($user, $newPassword);
        $user->setPlainPassword($newPassword);

        $token = $this->tokenService->createToken(
            Token::USER_PASSWORD_CHANGE_TYPE,
            DateHelper::carbonNow()->addDays(7)
        );
        $token->addField(TokenField::USER_ID_FIELD, (string) $user->getId());
        $token->addField(TokenField::CHANGED_NEW_HASHED_PASSWORD, $hashedPassword);
        $this->userAccountEmailService->changePasswordConfirmation(
            $user,
            $token->getTokenEntity()->getTokenString()
        );

        $this->em->persist($token->getTokenEntity());
    }

    public function setHashedPassword(User $user, string $newPasswordHash): void
    {
        $user->setPassword($newPasswordHash);

        $this->em->persist($user);
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->setPlainPassword($newPassword);
        $this->encodePasswordService->setEncodedPassword($user, $newPassword);

        $this->em->persist($user);
    }
}
