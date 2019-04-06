<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Helper\DateHelper;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\Secondary\UserAccountEmailService;
use Doctrine\ORM\EntityManagerInterface;

class ChangeEmailService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserAccountEmailService
     */
    private $userAccountEmailService;

    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(
        UserAccountEmailService $userAccountEmailService,
        TokenService $tokenService,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->userAccountEmailService = $userAccountEmailService;
        $this->tokenService = $tokenService;
    }

    public function sendConfirmation(User $user, string $newEmail): void
    {
        $token = $this->tokenService->createToken(
            Token::USER_EMAIL_CHANGE_TYPE,
            DateHelper::carbonNow()->addDays(7),
        );
        $token->addField(TokenField::USER_NEW_EMAIL_FIELD, $newEmail);
        $token->addField(TokenField::USER_ID_FIELD, (string) $user->getId());

        $this->userAccountEmailService->sendEmailChangeConfirmationToPreviousEmail(
            $user,
            $newEmail,
            $token->getTokenEntity()->getTokenString()
        );
        $this->userAccountEmailService->sendEmailChangeNotificationToNewEmail(
            $user,
            $newEmail,
            $token->getTokenEntity()->getTokenString()
        );

        $this->em->persist($token->getTokenEntity());
    }

    public function changeEmail(User $user, string $newEmail): void
    {
        $user->setEmail($newEmail);

        $this->em->persist($user);
    }
}
