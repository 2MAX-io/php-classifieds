<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;

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

    /**
     * @var int|null
     */
    public $amount;

    /**
     * @var string|null
     */
    public $currency;

    /**
     * @var string|null
     */
    public $gatewayTransactionId;

    /**
     * @var string|null
     */
    public $gatewayStatus;

    /**
     * @var Payment|null
     */
    public $paymentEntity;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getGatewayTransactionId(): ?string
    {
        return $this->gatewayTransactionId;
    }

    public function setGatewayTransactionId(?string $gatewayTransactionId): void
    {
        $this->gatewayTransactionId = $gatewayTransactionId;
    }

    public function getGatewayStatus(): ?string
    {
        return $this->gatewayStatus;
    }

    public function setGatewayStatus(?string $gatewayStatus): void
    {
        $this->gatewayStatus = $gatewayStatus;
    }

    public function getPaymentEntity(): ?Payment
    {
        return $this->paymentEntity;
    }

    public function setPaymentEntity(?Payment $paymentEntity): void
    {
        $this->paymentEntity = $paymentEntity;
    }
}
