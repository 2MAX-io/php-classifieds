<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Entity\PaymentForBalanceTopUp;
use App\Entity\PaymentForFeaturedPackage;
use App\Enum\ParamEnum;
use App\Exception\UserVisibleException;
use App\Service\Payment\PaymentService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentWaitController extends AbstractController
{
    /**
     * @Route("/user/payment/wait/{paymentAppToken}", name="app_payment_wait")
     */
    public function paymentWait(
        string $paymentAppToken
    ): Response {
        return $this->render('payment/payment_wait.html.twig', [
            ParamEnum::JSON_DATA => [
                'paymentAppToken' => $paymentAppToken,
            ],
        ]);
    }

    /**
     * @Route("/user/payment/wait/payment-wait-redirect/{paymentAppToken}", name="app_payment_wait_redirect", options={"expose": true})
     */
    public function paymentWaitRedirect(
        string $paymentAppToken,
        PaymentService $paymentService,
        LoggerInterface $logger
    ): Response {
        $paymentEntity = $paymentService->getPaymentEntity($paymentAppToken);
        $paymentForFeaturedPackage = $paymentEntity->getPaymentForFeaturedPackage();
        if ($paymentForFeaturedPackage instanceof PaymentForFeaturedPackage) {
            return $this->redirectToRoute('app_user_feature_listing', [
                'id' => $paymentForFeaturedPackage->getListing()->getId(),
            ]);
        }

        if ($paymentEntity->getPaymentForBalanceTopUp() instanceof PaymentForBalanceTopUp) {
            return $this->redirectToRoute('app_user_balance_top_up');
        }

        $logger->error('could not redirect from payment wait');
        throw new UserVisibleException('trans.Could not redirect from wait for payment page');
    }

    /**
     * @Route("/user/payment/payment-wait-refresh", name="app_payment_wait_refresh_ajax", options={"expose": true})
     */
    public function paymentWaitRefresh(
        Request $request,
        PaymentService $paymentService
    ): Response {
        $paymentEntity = $paymentService->getPaymentEntity($request->get('paymentAppToken'));

        return $this->json([
            'paymentComplete' => $paymentEntity->getBalanceUpdated(),
            'lastRefreshTime' => \date('H:i:s'),
        ]);
    }
}
