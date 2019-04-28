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
     * @var EmailService
     */
    private $emailService;

    /**
     * @var TokenService
     */
    private $tokenService;

    public function __construct(
        EntityManagerInterface $em,
        PasswordGenerateService $passwordGenerateService,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        EmailService $emailService
    ) {
        $this->em = $em;
        $this->passwordGenerateService = $passwordGenerateService;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailService = $emailService;
        $this->tokenService = $tokenService;
    }

    public function registerUser(string $email): User
    {
        $plainPassword = $this->passwordGenerateService->generatePassword();

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setRoles([User::ROLE_USER]);
        $user->setRegistrationDate(new \DateTime());
        $user->setEnabled(false);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $plainPassword)
        );
        $user->setPlainPassword($plainPassword);
        unset($plainPassword);

        $tokenDto = $this->tokenService->getTokenBuilder(
            Token::USER_REGISTER_TYPE,
            Carbon::now()->add('day', 7)
        );
        $tokenDto->addField(TokenField::USER_EMAIL_FIELD, (string) $user->getEmail());

        $this->em->persist($tokenDto->getTokenEntity());
        $this->em->persist($user);

        $this->emailService->sendRegisterEmail($user, $tokenDto->getTokenEntity()->getTokenString());

        return $user;
    }
}
