<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EncodePasswordService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function isPasswordValid(UserInterface $user, string $plaintextPassword): bool
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $plaintextPassword);
    }

    public function setEncodedPassword(
        User $user,
        string $plaintextPassword
    ): void {
        $user->setPassword($this->getEncodedPassword($user, $plaintextPassword));
    }

    public function getEncodedPassword(
        User $user,
        string $plaintextPassword
    ): string {
        return $this->userPasswordEncoder->encodePassword($user, $plaintextPassword);
    }
}
