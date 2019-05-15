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
     * @Route("/admin/red5/payment/", name="app_admin_payment_index", methods={"GET"})
     */
    public function index(Request $request, PaymentHistoryService $paymentHistoryService): Response
    {
        $this->denyUnlessAdmin();

        $paginationDto = $paymentHistoryService->getPaymentList((int) $request->get('page', 1));

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $paginationDto->getResults(),
            'pager' => $paginationDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/payment/{id}", name="app_admin_payment_show", methods={"GET"})
     */
    public function show(Payment $payment): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}
