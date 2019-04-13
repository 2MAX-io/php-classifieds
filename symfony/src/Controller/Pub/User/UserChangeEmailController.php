<?php

declare(strict_types=1);

namespace App\Controller\Pub\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserChangeEmailController extends AbstractController
{
    /**
     * @Route("/user/account/changeEmail", name="app_user_change_email")
     */
    public function changeEmail(): Response
    {
        return $this->render('user/change_email.html.twig', []);
    }
}
