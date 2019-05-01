<?php

declare(strict_types=1);

namespace App\Service\Payment;

class PaymentDto
{
    /**
     * @var string
     */
    public $paymentExecuteUrl;

    /**
     * @var string|null
     */
    public $returnUrl;

    public function setPaymentExecuteUrl(string $paymentExecuteUrl)
    {
        $this->paymentExecuteUrl = $paymentExecuteUrl;
    }

    public function getPaymentExecuteUrl(): string
    {
        return $this->paymentExecuteUrl;
    }

    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    public function setReturnUrl(?string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }
}
