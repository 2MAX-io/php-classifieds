<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use App\Service\Email\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateService
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

    public function __construct(
        EntityManagerInterface $em,
        PasswordGenerateService $passwordGenerateService,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailService $emailService
    ) {
        $this->em = $em;
        $this->passwordGenerateService = $passwordGenerateService;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailService = $emailService;
    }

    public function registerUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([User::ROLE_USER]);
        $plainPassword = $this->passwordGenerateService->generatePassword();
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $plainPassword)
        );
        $user->setPlainPassword($plainPassword);
        unset($plainPassword);

        $this->em->persist($user);

        $this->emailService->sendRegisterEmail($user);

        return $user;
    }
}
