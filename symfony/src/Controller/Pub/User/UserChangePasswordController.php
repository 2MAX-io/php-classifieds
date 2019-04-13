<?php

declare(strict_types=1);

namespace App\Controller\Pub\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserChangePasswordController extends AbstractController
{
    /**
     * @Route("/user/account/changePassword", name="app_user_change_password")
     */
    public function changePassword(): Response
    {
        return $this->render('user/change_password.html.twig', []);
    }
}
