<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\System\Admin;
use App\Entity\User;
use App\Enum\RuntimeCacheEnum;
use App\Enum\UserRoleEnum;
use App\Helper\SerializerHelper;
use App\Service\System\Cache\RuntimeCacheService;
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
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        Security $security,
        SessionInterface $session,
        RuntimeCacheService $runtimeCache,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->session = $session;
        $this->runtimeCache = $runtimeCache;
        $this->em = $em;
    }

    public function getUserOrNull(): ?User
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    public function getUser(): User
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            return $user;
        }

        throw new \RuntimeException('could not get current user');
    }

    public function getAdminOrNull(): ?Admin
    {
        $user = $this->security->getUser();

        if ($user instanceof Admin) {
            return $user;
        }

        return null;
    }

    public function isAdmin(): bool
    {
        return $this->getAdminOrNull() instanceof Admin;
    }

    public function isCurrentUser(User $user): bool
    {
        return $this->getUserOrNull() === $user;
    }

    public function isLoggedInUser(): bool
    {
        return $this->security->getUser() instanceof User;
    }

    /**
     * WARNING! do not use to authorize anything important, like authorizing admin actions
     *
     * only use to display links to admin panel, or show not activated listings, nothing more than that
     */
    public function isAdminInPublic(): bool
    {
        return $this->runtimeCache->get(RuntimeCacheEnum::ADMIN_IN_PUBLIC, function (): bool {
            return $this->isAdminInPublicNoCache();
        });
    }

    /**
     * WARNING! do not use to authorize anything important, like authorizing admin actions
     *
     * only use to display links to admin panel, or show not activated listings, nothing more than that
     */
    public function isAdminInPublicNoCache(): bool
    {
        $adminSerialized = $this->session->get('_security_admin', false);
        if (false === $adminSerialized) {
            return false;
        }

        /** @var TokenInterface $adminToken */
        $adminToken = SerializerHelper::safelyUnserialize($adminSerialized);

        /** @var Admin $admin */
        $admin = $adminToken->getUser();
        $admin = $this->em->getRepository(Admin::class)->find($admin->getId());

        if (!$admin instanceof Admin) {
            return false;
        }

        if (!\in_array(UserRoleEnum::ROLE_ADMIN, $admin->getRoles(), true)) {
            return false;
        }

        return true;
    }
}
