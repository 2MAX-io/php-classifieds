<?php

declare(strict_types=1);

namespace App\Controller\Admin\Base;

use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class AbstractAdminController extends AbstractController
{
    public function denyUnlessAdmin(): void
    {
        $token = $this->get('security.token_storage')->getToken();
        if (!$token instanceof TokenInterface) {
            throw new UnauthorizedHttpException('could not find token');
        }
        $user = $token->getUser();

        $this->denyAccessUnlessGranted(Admin::ROLE_ADMIN, $user);

        if (!$user instanceof Admin) {
            throw new UnauthorizedHttpException('only logged in admin is allowed here');
        }
    }
}
