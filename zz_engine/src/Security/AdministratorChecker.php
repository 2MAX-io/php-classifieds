<?php

declare(strict_types=1);

namespace App\Security;

use App\Security\Base\EnablableInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministratorChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $admin)
    {
        if (!$admin instanceof EnablableInterface) {
            return;
        }

        if (false === $admin->getEnabled()) {
            $e = new DisabledException("User [{$admin->getUsername()}] is disabled and can't be logged in");
            $e->setUser($admin);

            throw $e;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
