<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Form\User\Account\Register\RegisterUserDto;
use App\Helper\DateHelper;
use App\Helper\StringHelper;
use App\Service\System\Token\TokenService;
use App\Service\User\Account\Secondary\PasswordGenerateService;
use App\Service\User\Account\Secondary\UserAccountEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PasswordGenerateService
     */
    private $passwordGenerateService;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserAccountEmailService
     */
    private $userAccountEmailService;

    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(
        PasswordGenerateService $passwordGenerateService,
        UserPasswordEncoderInterface $passwordEncoder,
        UserAccountEmailService $userAccountEmailService,
        TokenService $tokenService,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->passwordGenerateService = $passwordGenerateService;
        $this->passwordEncoder = $passwordEncoder;
        $this->userAccountEmailService = $userAccountEmailService;
        $this->tokenService = $tokenService;
    }

    public function registerUser(RegisterUserDto $registerDto): User
    {
        $user = $this->getUser($registerDto->getEmail());
        $user = $this->setPasswordAndSendConfirmation($user, $registerDto);

        return $user;
    }

    public function setPasswordAndSendConfirmation(User $user, RegisterUserDto $registerDto): User
    {
        if ($user->getEmail() !== $registerDto->getEmail()) {
            throw new \RuntimeException('user email and register dto email does not match');
        }

        $user = $this->getUser(
            $registerDto->getEmail(),
            $registerDto->getPassword(),
            $user,
        );
        $tokenDto = $this->tokenService->createToken(
            Token::USER_REGISTER_TYPE,
            DateHelper::carbonNow()->addDays(7),
        );
        $tokenDto->addField(TokenField::USER_EMAIL_FIELD, (string) $user->getEmail());

        $this->em->persist($tokenDto->getTokenEntity());
        $this->em->persist($user);

        $this->userAccountEmailService->sendRegisterEmail($user, $tokenDto->getTokenEntity()->getTokenString());

        return $user;
    }

    public function getUser(string $email, string $plainPassword = null, User $user = null): User
    {
        if (!$plainPassword) {
            $plainPassword = $this->passwordGenerateService->generatePassword();
        }

        if (StringHelper::emptyTrim($plainPassword)) {
            throw new \RuntimeException('password can not be empty');
        }

        if (null === $user) {
            $user = new User();
        }
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setRoles([User::ROLE_USER]);
        $user->setRegistrationDate(DateHelper::create());
        $user->setEnabled(false);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $plainPassword)
        );
        $user->setPlainPassword($plainPassword);

        return $user;
    }
}
