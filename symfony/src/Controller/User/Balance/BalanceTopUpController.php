<?php

declare(strict_types=1);

namespace App\Controller\User\Balance;

use App\Controller\User\Base\AbstractUserController;
use App\Form\TopUpBalanceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalanceTopUpController extends AbstractUserController
{
    /**
     * @Route("/user/balance/top-up", name="app_user_balance_top_up")
     */
    public function balanceTopUp(Request $request): Response
    {
        $this->dennyUnlessUser();

        $form = $this->createForm(TopUpBalanceType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('user/balance/balance_top_up.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
