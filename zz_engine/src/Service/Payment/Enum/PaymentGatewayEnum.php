<?php
declare(strict_types=1);

namespace App\Service\Payment\Enum;

class PaymentGatewayEnum
{
    public const PAYPAL = 'paypal';
    public const PAYPAL_NATIVE = 'paypal_native';
    public const PRZELEWY24 = 'przelewy24';
}