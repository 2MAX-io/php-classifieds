<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Admin;
use App\Entity\User;
use App\Service\User\RoleInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Security $security, SessionInterface $session, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->session = $session;
        $this->em = $em;
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
     * WARNING! do not use to authorize anything important, like authorizing admin actions
     *
     * only use to display links to admin panel, or show not activated listings, nothing more than that
     *
     * @return bool
     */
    public function lowSecurityCheckIsAdminInPublic(): bool
    {
        $adminSerialized = $this->session->get('_security_admin', false);

        if (false === $adminSerialized) {
            return false;
        }

        /** @var TokenInterface $adminToken */
        $adminToken = unserialize($adminSerialized);

        $admin = $adminToken->getUser();
        $admin = $this->em->merge($admin);
        $this->em->refresh($admin);

        if (!$admin instanceof Admin) {
            return false;
        }

        if (!in_array(RoleInterface::ROLE_ADMIN, $admin->getRoles(), true)) {
            return false;
        }

        return true;
    }
}
