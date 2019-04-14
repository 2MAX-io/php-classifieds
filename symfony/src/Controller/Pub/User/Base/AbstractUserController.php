<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Base;

use App\Entity\Admin;
use App\Entity\Listing;
use App\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractUserController extends AbstractController
{
    public function dennyUnlessCurrentUserListing(Listing $listing): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof User) {
            $this->denyAccessUnlessGranted(Admin::ROLE_USER, $user);
        }

        if ($user !== $listing->getUser()) {
            throw new UnauthorizedHttpException('user of listing do not match current user');
        }
    }
}