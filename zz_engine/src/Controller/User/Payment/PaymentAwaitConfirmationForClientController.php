<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\PaymentForBalanceTopUp;
use App\Entity\PaymentForFeaturedPackage;
use App\Enum\ParamEnum;
use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
use App\Service\Payment\PaymentService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentAwaitConfirmationForClientController extends AbstractController
{
    /**
     * @Route("/user/payment/await-payment-confirmation/await-confirmation/{paymentAppToken}", name="app_payment_await_confirmation")
     */
    public function awaitPaymentConfirmation(string $paymentAppToken): Response
    {
        return $this->render(
            'payment/payment_await_confirmation.html.twig',
            [
                ParamEnum::DATA_FOR_JS => [
                    ParamEnum::PAYMENT_APP_TOKEN => $paymentAppToken,
                ],
            ]
        );
    }

    /**
     * @Route(
     *     "/user/payment/await-payment-confirmation/refresh",
     *     name="app_payment_status_refresh",
     *     options={"expose": true},
     * )
     */
    public function paymentStatusRefresh(
        Request $request,
        PaymentService $paymentService
    ): Response {
        $paymentEntity = $paymentService->getPaymentEntity($request->get(ParamEnum::PAYMENT_APP_TOKEN));

        return $this->json([
            'paymentComplete' => $paymentEntity->getPaid(),
            'lastRefreshTime' => DateHelper::date('H:i:s'),
        ]);
    }

    /**
     * @Route(
     *     "/user/payment/await-payment-confirmation/redirect-destination/{paymentAppToken}",
     *     name="app_payment_await_confirmation_redirect",
     *     options={"expose": true},
     * )
     */
    public function getRedirectDestinationAfterPaymentConfirmation(
        string $paymentAppToken,
        PaymentService $paymentService,
        LoggerInterface $logger
    ): Response {
        $paymentEntity = $paymentService->getPaymentEntity($paymentAppToken);
        $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();
        if ($paymentForFeaturedPackage instanceof PaymentForFeaturedPackage) {
            return $this->redirectToRoute('app_user_feature_listing', [
                'id' => $paymentForFeaturedPackage->getListingNotNull()->getId(),
            ]);
        }

        if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
            return $this->redirectToRoute('app_user_balance_top_up');
        }

        $logger->error('redirect route not found in payment wait redirect');

        throw new UserVisibleException('trans.Destination page not found, if you have been charged and did not receive service, please contact us');
    }
}
