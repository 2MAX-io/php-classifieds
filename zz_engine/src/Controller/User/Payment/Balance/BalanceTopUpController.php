<?php

declare(strict_types=1);

namespace App\Controller\User\Payment\Balance;

use App\Controller\User\Base\AbstractUserController;
use App\Form\TopUpBalanceType;
use App\Helper\IntegerHelper;
use App\Security\CurrentUserService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalanceTopUpController extends AbstractUserController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user/balance/top-up", name="app_user_balance_top_up")
     */
    public function balanceTopUp(
        Request $request,
        UserBalanceService $userBalanceService,
        PaymentService $paymentService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessUser();

        $form = $this->createForm(TopUpBalanceType::class, []);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentDto = $paymentService->createPaymentForTopUp(
                $currentUserService->getUser(),
                IntegerHelper::toInteger($form->get(TopUpBalanceType::TOP_UP_AMOUNT)->getData() * 100),
            );
            $this->em->flush();

            return $this->redirect($paymentDto->getMakePaymentUrl());
        }

        return $this->render(
            'user/balance/balance_top_up.html.twig',
            [
                'form' => $form->createView(),
                'userBalance' => $userBalanceService->getCurrentBalance($currentUserService->getUser()),
            ]
        );
    }
}
