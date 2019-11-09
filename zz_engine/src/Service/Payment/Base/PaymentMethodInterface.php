<?php

declare(strict_types=1);

namespace App\Service\Payment\Base;

use App\Service\Payment\ConfirmPaymentConfigDto;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;

interface PaymentMethodInterface
{
    public function createPayment(PaymentDto $paymentDto): void;

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto;
}
