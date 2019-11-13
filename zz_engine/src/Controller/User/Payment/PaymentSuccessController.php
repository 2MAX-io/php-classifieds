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
            $completePurchaseDto = $paymentService->process($confirmPaymentConfigDto);
            if ($em->getConnection()->isTransactionActive()) {
                $em->flush();
                $em->commit();
            }
            if ($completePurchaseDto->isSuccess()) {
                return $completePurchaseDto->getRedirectResponse();
            }
        } catch (\Throwable $e) {
            $em->rollback();

            $logger->error('error on payment success', ExceptionHelper::flatten($e));

            throw $this->getGeneralException($e);
        }

        $logger->error('could not find any valid response for success payment');

        throw $this->getGeneralException();
    }

    private function getGeneralException(\Exception $e = null): \Throwable
    {
        return new UserVisibleException('trans.Could not process payment, if you have been charged and did not receive service, please contact us', [], 0, $e);
    }
}
