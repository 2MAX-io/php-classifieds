<?php

declare(strict_types=1);

namespace App\Service\Payment\Base;

use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;
use Symfony\Component\HttpFoundation\Request;

interface PaymentMethodInterface
{
    public function createPayment(PaymentDto $paymentDto): void;

    public function confirmPayment(Request $request): ConfirmPaymentDto;
}
