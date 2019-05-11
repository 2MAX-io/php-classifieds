<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Entity\User;

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
    public $gatewayToken;

    /**
     * @var string|null
     */
    public $gatewayCancelUrl;

    /**
     * @var string|null
     */
    public $gatewayStatus;

    /**
     * @var Payment|null
     */
    public $paymentEntity;

    /**
     * @var User|null
     */
    public $user;

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

    public function getGatewayCancelUrl(): ?string
    {
        return $this->gatewayCancelUrl;
    }

    public function setGatewayCancelUrl(?string $gatewayCancelUrl): void
    {
        $this->gatewayCancelUrl = $gatewayCancelUrl;
    }

    public function getGatewayToken(): ?string
    {
        return $this->gatewayToken;
    }

    public function setGatewayToken(?string $gatewayToken): void
    {
        $this->gatewayToken = $gatewayToken;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
