<?php

declare(strict_types=1);

namespace App\Controller\Admin\Base;

use App\Entity\Admin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAdminController extends AbstractController
{
    public function denyUnlessAdmin(): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $this->denyAccessUnlessGranted(Admin::ROLE_ADMIN, $user);

        if (!$user instanceof Admin) {
            $exception = $this->createAccessDeniedException('Access Denied.');
            $exception->setSubject($user);

            throw $exception;
        }
    }
}
