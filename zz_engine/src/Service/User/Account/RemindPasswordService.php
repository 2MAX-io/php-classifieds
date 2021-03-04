<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
use App\Repository\UserRepository;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\Secondary\EncodePasswordService;
use App\Service\User\Account\Secondary\PasswordGenerateService;
use App\Service\User\Account\Secondary\UserAccountEmailService;
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

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        EncodePasswordService $encodePasswordService,
        PasswordGenerateService $passwordGenerateService,
        UserAccountEmailService $userAccountEmailService,
        UserRepository $userRepository,
        TokenService $tokenService,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
        $this->tokenService = $tokenService;
        $this->userAccountEmailService = $userAccountEmailService;
        $this->passwordGenerateService = $passwordGenerateService;
        $this->userRepository = $userRepository;
    }

    public function sendRemindConfirmation(string $email): void
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserVisibleException('trans.email address not found');
        }
        $newPassword = $this->passwordGenerateService->generatePassword();
        $hashedPassword = $this->encodePasswordService->getEncodedPassword($user, $newPassword);
        $user->setPlainPassword($newPassword);

        $tokenDto = $this->tokenService->createToken(
            Token::USER_PASSWORD_REMIND,
            DateHelper::carbonNow()->addDays(7),
        );
        $tokenDto->addField(TokenField::USER_ID_FIELD, (string) $user->getId());
        $tokenDto->addField(TokenField::REMINDED_HASHED_PASSWORD, $hashedPassword);

        $this->em->persist($tokenDto->getTokenEntity());

        $this->userAccountEmailService->remindPasswordConfirmation(
            $user,
            $tokenDto->getTokenEntity()->getTokenString()
        );
    }

    public function setHashedPassword(User $user, string $newPasswordHash): void
    {
        $user->setPassword($newPasswordHash);

        $this->em->persist($user);
    }
}
