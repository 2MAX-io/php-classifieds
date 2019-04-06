<?php

declare(strict_types=1);

namespace App\Security;

use App\Security\Base\EnablableInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCheckerService implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof EnablableInterface) {
            return;
        }

        if (false === $user->getEnabled()) {
            $e = new DisabledException();
            $e->setUser($user);

            throw $e;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
