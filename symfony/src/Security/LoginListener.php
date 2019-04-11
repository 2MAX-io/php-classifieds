<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $userInterface = $event->getAuthenticationToken()->getUser();

        if ($userInterface instanceof User) {
            $this->user($userInterface);
        }

        $this->em->flush();
    }

    private function user(User $user)
    {
        $user->setLastLogin(new \DateTime());
        $this->em->persist($user);
    }
}
