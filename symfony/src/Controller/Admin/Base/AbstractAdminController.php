<?php

declare(strict_types=1);

namespace App\Controller\Admin\Base;

use App\Entity\Admin;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAdminController extends AbstractController
{
    /**
     * Override method to call #containerInitialized method when container set.
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->containerInitialized();
    }

    public function denyUnlessAdmin(): void
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof Admin) {
            $this->denyAccessUnlessGranted(Admin::ROLE_ADMIN, $user);
        }
    }

    private function containerInitialized(): void
    {
        $this->denyUnlessAdmin();
    }
}
