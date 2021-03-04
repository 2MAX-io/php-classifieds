<?php

declare(strict_types=1);

namespace App\Service\Payment\Base;

use App\Service\Payment\Dto\ConfirmPaymentConfigDto;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Dto\PaymentDto;

interface PaymentGatewayInterface
{
    public static function getName(): string;

    public function createPayment(PaymentDto $paymentDto): void;

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto;

    public function getGatewayMode(): string;
}
