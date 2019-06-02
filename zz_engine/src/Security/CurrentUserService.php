<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Admin;
use App\Entity\User;
use App\Helper\SerializerHelper;
use App\Service\User\RoleInterface;
use App\System\Cache\RuntimeCacheEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Simple\ArrayCache;
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

    /**
     * @var ArrayCache
     */
    private $arrayCache;

    public function __construct(Security $security, SessionInterface $session, ArrayCache $arrayCache, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->session = $session;
        $this->em = $em;
        $this->arrayCache = $arrayCache;
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

    public function isCurrentUser(User $user): bool
    {
        return $this->getUser() === $user;
    }

    public function isLoggedInUser(): bool
    {
        return $this->security->getUser() instanceof User;
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
        if ($this->arrayCache->has(RuntimeCacheEnum::ADMIN_IN_PUBLIC)) {
            return (bool) $this->arrayCache->get(RuntimeCacheEnum::ADMIN_IN_PUBLIC);
        }

        $return = $this->lowSecurityCheckIsAdminInPublicNoCache();
        $this->arrayCache->set(RuntimeCacheEnum::ADMIN_IN_PUBLIC, $return);

        return $return;
    }

    /**
     * WARNING! do not use to authorize anything important, like authorizing admin actions
     *
     * only use to display links to admin panel, or show not activated listings, nothing more than that
     *
     * @return bool
     */
    public function lowSecurityCheckIsAdminInPublicNoCache(): bool
    {
        $adminSerialized = $this->session->get('_security_admin', false);

        if (false === $adminSerialized) {
            return false;
        }

        /** @var TokenInterface $adminToken */
        $adminToken = SerializerHelper::safelyUnserialize($adminSerialized);

        $admin = $adminToken->getUser();
        $admin = $this->em->merge($admin); // merge does refresh

        if (!$admin instanceof Admin) {
            return false;
        }

        if (!\in_array(RoleInterface::ROLE_ADMIN, $admin->getRoles(), true)) {
            return false;
        }

        return true;
    }
}
