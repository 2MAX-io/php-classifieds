<?php

declare(strict_types=1);

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Payment;
use App\Service\Money\PaymentHistoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/payment/", name="app_admin_payment_list", methods={"GET"})
     */
    public function paymentListForAdmin(Request $request, PaymentHistoryService $paymentHistoryService): Response
    {
        $this->denyUnlessAdmin();

        $paginationDto = $paymentHistoryService->getPaymentList((int) $request->get('page', 1));
        /** @var Payment[] $payments */
        $payments = $paginationDto->getResults();

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $payments,
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/payment/{id}/show-payment", name="app_admin_payment_show", methods={"GET"})
     */
    public function showPaymentForAdmin(Payment $payment): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}
