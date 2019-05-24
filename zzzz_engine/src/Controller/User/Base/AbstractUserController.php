<?php

declare(strict_types=1);

namespace App\Controller\User\Base;

use App\Entity\Admin;
use App\Entity\Listing;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractUserController extends AbstractController
{
    /**
     * @param bool $ignoreAdminDeleted used to delete listing by user, when listing is admin deleted
     */
    public function dennyUnlessCurrentUserAllowed(Listing $listing, bool $ignoreAdminDeleted = false): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted(Admin::ROLE_USER, $user);
        }

        if ($user !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($listing->getUserRemoved() || ($listing->getAdminRemoved() && !$ignoreAdminDeleted)) {
            throw new UnauthorizedHttpException('listing has been removed');
        }
    }

    public function dennyUnlessUser(): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted(Admin::ROLE_USER, $user);
        }
    }
}
