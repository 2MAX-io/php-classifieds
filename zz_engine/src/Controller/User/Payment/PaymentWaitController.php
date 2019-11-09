<?php

declare(strict_types=1);

namespace App\Controller\User\Payment;

use App\Enum\ParamEnum;
use App\Exception\UserVisibleException;
use App\Service\Listing\Featured\FeaturedListingService;
use App\Service\Money\UserBalanceService;
use App\Service\Payment\PaymentService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentWaitController extends AbstractController
{
    /**
     * @Route("/user/payment/wait/{paymentAppToken}", name="app_payment_wait")
     */
    public function paymentWait(
        Request $request,
        string $paymentAppToken,
        PaymentService $paymentService,
        UserBalanceService $userBalanceService,
        FeaturedListingService $featuredListingService,
        SettingsService $settingsService,
        EntityManagerInterface $em,
        TranslatorInterface $trans,
        LoggerInterface $logger
    ): Response {
        return $this->render('payment/payment_wait.html.twig', [
            ParamEnum::JSON_DATA => [
                'paymentAppToken' => $paymentAppToken,
            ],
        ]);
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

    /**
     * @throws UserVisibleException
     */
    private function throwGeneralException(): void
    {
        throw new UserVisibleException('trans.Could not process payment, if you have been charged and did not receive service, please contact us');
    }
}
