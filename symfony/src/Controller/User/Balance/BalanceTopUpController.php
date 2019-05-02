<?php

declare(strict_types=1);

namespace App\Controller\User\Balance;

use App\Controller\User\Base\AbstractUserController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalanceTopUpController extends AbstractUserController
{
    /**
     * @Route("/user/balance/top-up", name="app_user_balance_top_up")
     */
    public function balanceTopUp(): Response
    {
        $this->dennyUnlessUser();

        return $this->render('user/balance/balance_top_up.html.twig', []);
    }
}
