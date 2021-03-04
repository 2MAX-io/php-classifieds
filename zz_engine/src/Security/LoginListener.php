<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Helper\DateHelper;
use App\Helper\RandomHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $em)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $userInterface = $event->getAuthenticationToken()->getUser();

        if ($userInterface instanceof User) {
            $this->updateLastLoginOfUser($userInterface);
            $this->migrateLegacyPassword($userInterface, $event);
        }

        $this->em->flush();
    }

    private function updateLastLoginOfUser(User $user): void
    {
        $user->setLastLogin(DateHelper::create());
        $this->em->persist($user);
    }

    private function migrateLegacyPassword(User $user, InteractiveLoginEvent $event): void
    {
        if (null === $user->getEncoderName()) {
            return;
        }

        if (false === $event->getRequest()->request->get('password', false)) {
            return;
        }

        $user->setPassword('removed '.RandomHelper::string(20)); // safe guard for empty passwords
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user,
                $event->getRequest()->request->get('password')
            )
        );

        $this->em->persist($user);
    }
}
