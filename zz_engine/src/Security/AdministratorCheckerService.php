<?php

declare(strict_types=1);

namespace App\Security;

use App\Security\Base\EnablableInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministratorCheckerService implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $admin): void
    {
        if (!$admin instanceof EnablableInterface) {
            return;
        }

        if (false === $admin->getEnabled()) {
            $e = new DisabledException();
            $e->setUser($admin);

            throw $e;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
