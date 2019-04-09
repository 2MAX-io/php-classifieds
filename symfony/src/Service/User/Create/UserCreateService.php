<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
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

    public function __construct(
        EntityManagerInterface $em,
        PasswordGenerateService $passwordGenerateService,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->em = $em;
        $this->passwordGenerateService = $passwordGenerateService;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function registerUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([User::ROLE_USER]);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $this->passwordGenerateService->generatePassword())
        );

        $this->em->persist($user);

        return $user;
    }
}
