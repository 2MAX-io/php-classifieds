<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\Payment;
use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Service\Payment\ConfirmPaymentConfigDto;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentSuccessController extends AbstractController
{
    /**
     * @Route("/user/payment/success/{paymentAppToken}", name="app_payment")
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

            if (!$confirmPaymentDto->isConfirmed()) {
                $logger->error('payment is not confirmed', [$confirmPaymentDto]);

                throw $this->getGeneralException();
            }

            if ($paymentService->isBalanceUpdated($confirmPaymentDto)) {
                $logger->error('balance has been already updated', [$confirmPaymentDto]);

                throw $this->getGeneralException();
            }

            $paymentEntity = $confirmPaymentDto->getPaymentEntity();
            if (!$paymentEntity instanceof Payment) {
                $logger->error('could not find payment entity', [$confirmPaymentDto]);

                throw $this->getGeneralException();
            }

            if ($confirmPaymentDto->getGatewayAmount() !== $paymentEntity->getAmount()) {
                $logger->error('paid amount do not match between gateway and payment entity', [$confirmPaymentDto]);

                throw $this->getGeneralException();
            }

            $completePurchaseDto = $paymentService->completePurchase($confirmPaymentDto);
            if ($completePurchaseDto->isRedirect()) {
                return $completePurchaseDto->getResponse();
            }

            if ($em->getConnection()->isTransactionActive()) {
                $em->commit();
            }

        } catch (\Throwable $e) {
            $em->rollback();

            $logger->error('error while processing payment', ExceptionHelper::flatten($e));

            throw $this->getGeneralException($e);
        }

        $logger->error('could not find any valid response for success payment');

        throw $this->getGeneralException();
    }

    private function getGeneralException(\Throwable $e = null): \Throwable
    {
        return new UserVisibleException('trans.Could not process payment, if you have been charged and did not receive service, please contact us', [], 0, $e);
    }
}
