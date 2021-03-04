<?php

declare(strict_types=1);

namespace App\Controller\User\Balance;

use App\Controller\User\Base\AbstractUserController;
use App\Security\CurrentUserService;
use App\Service\Money\UserBalanceHistoryService;
use App\Service\Money\UserBalanceService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalanceHistoryController extends AbstractUserController
{
    /**
     * @Route("/user/balance/history", name="app_user_balance_history")
     */
    public function balanceHistory(
        UserBalanceService $userBalanceService,
        UserBalanceHistoryService $userBalanceHistoryService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessUser();
        $currentUser = $currentUserService->getUser();

        return $this->render(
            'user/balance/balance_history.html.twig',
            [
                'userBalance' => $userBalanceService->getCurrentBalance($currentUser),
                'userBalanceHistoryList' => $userBalanceHistoryService->getHistoryList($currentUser),
            ]
        );
    }
}
