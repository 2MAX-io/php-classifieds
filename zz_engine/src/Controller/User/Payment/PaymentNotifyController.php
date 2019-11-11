<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Service\Payment\Dto\ConfirmPaymentConfigDto;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentNotifyController extends AbstractController
{
    /**
     * @Route("/payment/notify/{paymentAppToken}", name="app_payment_notify")
     */
    public function payment(
        Request $request,
        string $paymentAppToken,
        PaymentService $paymentService,
        SettingsService $settingsService,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): Response {
        if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
            throw new UserVisibleException('trans.Payments have been disabled');
        }

        $em->beginTransaction();

        try {
            $confirmPaymentConfigDto = new ConfirmPaymentConfigDto();
            $confirmPaymentConfigDto->setRequest($request);
            $confirmPaymentConfigDto->setPaymentAppToken($paymentAppToken);
            $confirmPaymentDto = $paymentService->confirmPayment($confirmPaymentConfigDto);
            $paymentService->validate($confirmPaymentDto);
            $completePurchaseDto = $paymentService->completePurchase($confirmPaymentDto);
            if ($completePurchaseDto->isSuccess()) {
                return new Response('', Response::HTTP_OK);
            }

            if ($em->getConnection()->isTransactionActive()) {
                $em->commit();
            }
        } catch (\Throwable $e) {
            $em->rollback();

            $logger->error('error getting payment notification', ExceptionHelper::flatten($e));

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
