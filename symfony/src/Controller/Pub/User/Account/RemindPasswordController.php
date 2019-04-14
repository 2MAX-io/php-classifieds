<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Account;

use App\Form\User\RemindPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemindPasswordController extends AbstractController
{
    /**
     * @Route("/remind-password", name="app_remind_password")
     */
    public function remindPassword(): Response
    {
        $form = $this->createForm(RemindPasswordType::class, []);

        return $this->render('user/account/remind_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
