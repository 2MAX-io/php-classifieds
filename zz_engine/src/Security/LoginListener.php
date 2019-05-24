<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Helper\Random;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $userInterface = $event->getAuthenticationToken()->getUser();

        if ($userInterface instanceof User) {
            $this->updateLastLoginOfUser($userInterface);
            $this->migrateLegacyPassword($userInterface, $event);
        }

        $this->em->flush();
    }

    private function updateLastLoginOfUser(User $user)
    {
        $user->setLastLogin(new \DateTime());
        $this->em->persist($user);
    }

    private function migrateLegacyPassword(User $user, InteractiveLoginEvent $event)
    {
        if ($user->getEncoderName() === null) {
            return;
        }

        if (false === $event->getRequest()->request->get('password', false)) {
            return;
        }

        $user->setPassword('removed ' . Random::string(20));
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user,
                $event->getRequest()->request->get('password')
            )
        );

        $this->em->persist($user);
    }
}
