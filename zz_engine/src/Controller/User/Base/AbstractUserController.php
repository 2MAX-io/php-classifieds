<?php

declare(strict_types=1);

namespace App\Controller\User\Base;

use App\Entity\Admin;
use App\Entity\Listing;
use App\Entity\User;
use App\Exception\UserVisibleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractUserController extends AbstractController
{
    /**
     * @param bool $ignoreAdminDeleted used to delete listing by user, when listing is admin deleted
     */
    public function dennyUnlessCurrentUserAllowed(Listing $listing, bool $ignoreAdminDeleted = false): void
    {
        $token = $this->get('security.token_storage')->getToken();
        if ($token === null) {
            throw new UnauthorizedHttpException('token is empty');
        }
        $user = $token->getUser();

        $this->denyAccessUnlessGranted(Admin::ROLE_USER, $user);

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('only logged in user is allowed here');
        }

        if ($user !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($listing->getUserRemoved() || ($listing->getAdminRemoved() && !$ignoreAdminDeleted)) {
            throw new UserVisibleException('trans.Listing has been removed');
        }
    }

    public function dennyUnlessUser(): void
    {
        $token = $this->get('security.token_storage')->getToken();
        if ($token === null) {
            throw new UnauthorizedHttpException('token is empty');
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('only logged in user is allowed here');
        }
    }
}
