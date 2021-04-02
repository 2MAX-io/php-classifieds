<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentNotifyByPaymentProcessorController extends AbstractController
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
     * @Route("/private/payment/notify/{paymentAppToken}", name="app_payment_gateway_notify")
     */
    public function payment(
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
            $confirmPaymentDto = new ConfirmPaymentDto();
            $confirmPaymentDto->setRequest($request);
            $confirmPaymentDto->setPaymentAppToken($paymentAppToken);
            $completePurchaseDto = $paymentService->confirmPayment($confirmPaymentDto);
            if ($this->em->getConnection()->isTransactionActive()) {
                $this->em->flush();
                $this->em->commit();
            }
            if ($completePurchaseDto->isSuccess()) {
                return new Response('', Response::HTTP_OK);
            }
        } catch (\Throwable $e) {
            $this->em->rollback();

            $logger->error('error getting payment notification', ExceptionHelper::flatten($e));

            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
