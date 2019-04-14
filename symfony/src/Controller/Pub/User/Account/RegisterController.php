<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Account;

use App\Service\User\Create\RegisterConfirmService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register/confirm/{token}", name="app_register_confirm")
     */
    public function registerConfirm(string $token, RegisterConfirmService $registerConfirmService): Response
    {
        $registerConfirmService->confirmRegistration($token);

        return $this->render('user/account/register_confirm.html.twig', [
        ]);
    }
}
