<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\Token;
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
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setRoles([User::ROLE_USER]);
        $user->setFirstCreatedDate(new \DateTime());
        $user->setEnabled(false);
        $plainPassword = $this->passwordGenerateService->generatePassword();
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $plainPassword)
        );
        $user->setPlainPassword($plainPassword);
        unset($plainPassword);
        $token = $this->tokenService->createToken($email, Token::USER_REGISTER_TYPE, Carbon::now()->add('day', 7));
        $user->setConfirmationToken($token);

        $this->em->persist($user);

        $this->emailService->sendRegisterEmail($user);

        return $user;
    }

    public function hasUser(string $email): bool
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        return $user instanceof User;
    }
}
