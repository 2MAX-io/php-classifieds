<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Admin;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class CurrentUserService
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser(): ?User
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    public function getAdmin(): ?Admin
    {
        $user = $this->security->getUser();

        if ($user instanceof Admin) {
            return $user;
        }

        return null;
    }

    public function isAdmin(): bool
    {
        return $this->getAdmin() instanceof Admin;
    }
}
