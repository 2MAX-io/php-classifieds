<?php

namespace App\Controller\Admin\Payment;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/payment/", name="app_admin_payment_index", methods={"GET"})
     */
    public function index(PaymentRepository $paymentRepository): Response
    {
        $this->denyUnlessAdmin();

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $paymentRepository->findAll(),
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
