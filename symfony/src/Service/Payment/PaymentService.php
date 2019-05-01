<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Payment\Method\PayPalPaymentMethod;
use Symfony\Component\HttpFoundation\Request;

class PaymentService
{
    /**
     * @var PayPalPaymentMethod
     */
    private $payPalPaymentMethod;

    public function __construct(PayPalPaymentMethod $payPalPaymentMethod)
    {
        $this->payPalPaymentMethod = $payPalPaymentMethod;
    }

    public function createPayment(): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $this->payPalPaymentMethod->createPayment($paymentDto);

        return $paymentDto;
    }

    public function confirmPayment(Request $request)
    {
        $this->payPalPaymentMethod->confirmPayment($request);
    }
}
