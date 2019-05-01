<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\User;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/user/payment", name="app_payment")
     */
    public function index(Request $request, PaymentService $paymentService, UserBalanceService $userBalanceService, EntityManagerInterface $em): Response
    {
        $em->beginTransaction();

        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $confirmPaymentDto = $paymentService->confirmPayment($request, $confirmPaymentDto);

            if ($confirmPaymentDto->isConfirmed() && !$paymentService->isBalanceUpdated($confirmPaymentDto)) {
                $userBalanceService->addBalance(1000, $em->getRepository(User::class)->find(1));
                $paymentService->markBalanceUpdated($confirmPaymentDto);
            }
            $em->flush();
            $em->commit();
        } catch (\Throwable $e) {
            $em->rollback();
        }

        return new Response('ok');
    }
}
