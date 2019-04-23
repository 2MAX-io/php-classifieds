<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Base;

use App\Entity\Admin;
use App\Entity\Listing;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractUserController extends AbstractController
{
    public function dennyUnlessCurrentUserAllowed(Listing $listing): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted(Admin::ROLE_USER, $user);
        }

        if ($user !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }

        if ($listing->getUserRemoved() || $listing->getAdminRemoved()) {
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
