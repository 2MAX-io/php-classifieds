<?php

declare(strict_types=1);

namespace App\Controller\Pub\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register/confirm", name="app_register_confirm")
     */
    public function registerConfirm(Request $request): Response
    {
        return $this->render('user/register/confirm/register_confirm.html.twig', [
        ]);
    }
}
