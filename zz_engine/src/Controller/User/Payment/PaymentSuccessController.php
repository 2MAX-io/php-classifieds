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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/user/payment/success/{paymentAppToken}", name="app_payment_success")
     */
    public function paymentSuccess(
        Request $request,
        string $paymentAppToken,
        PaymentService $paymentService,
        SettingsService $settingsService,
        LoggerInterface $logger
    ): Response {
        if (!$settingsService->getSettingsDto()->isPaymentAllowed()) {
            throw new UserVisibleException('trans.Payments have been disabled');
        }

        try {
            $this->em->beginTransaction();
            $confirmPaymentConfigDto = new ConfirmPaymentConfigDto();
            $confirmPaymentConfigDto->setRequest($request);
            $confirmPaymentConfigDto->setPaymentAppToken($paymentAppToken);
            $completePurchaseDto = $paymentService->confirmPayment($confirmPaymentConfigDto);
            if ($this->em->getConnection()->isTransactionActive()) {
                $this->em->flush();
                $this->em->commit();
            }
            if ($completePurchaseDto->isSuccess()) {
                return $completePurchaseDto->getRedirectResponseNotNull();
            }
        } catch (\Throwable $e) {
            $this->em->rollback();

            $logger->error('error on payment success', ExceptionHelper::flatten($e));

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
