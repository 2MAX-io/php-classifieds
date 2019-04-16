<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Admin;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class CurrentUserService
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(Security $security, SessionInterface $session)
    {
        $this->security = $security;
        $this->session = $session;
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

    /**
     * WARNING! do not use to authorize anything important
     *
     * only use to display links to admin panel, or show hidden listings, nothing more than that
     *
     * @return bool
     */
    public function lowSecurityCheckIsAdminInPublic(): bool
    {
        return $this->session->get('_security_admin', false) !== false;
    }
}
