<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\FeaturedPackage;
use App\Entity\Payment;
use App\Entity\User;

class PaymentDto
{
    /**
     * @var string
     */
    private $paymentType;

    /**
     * @var string
     */
    private $paymentDescription;

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
    public $gatewayPaymentId;

    /**
     * @var string|null
     */
    public $gatewayToken;

    /**
     * @var string|null
     */
    public $gatewayStatus;

    /**
     * @var string|null
     */
    public $gatewayPaymentDescription;

    /**
     * @var Payment|null
     */
    public $paymentEntity;

    /**
     * @var User|null
     */
    public $user;

    public function setPaymentExecuteUrl(string $paymentExecuteUrl): void
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

    public function getGatewayPaymentId(): ?string
    {
        return $this->gatewayPaymentId;
    }

    public function setGatewayPaymentId(?string $gatewayPaymentId): void
    {
        $this->gatewayPaymentId = $gatewayPaymentId;
    }

    public function getGatewayPaymentDescription(): ?string
    {
        return $this->gatewayPaymentDescription;
    }

    public function setGatewayPaymentDescription(?string $gatewayPaymentDescription): void
    {
        $this->gatewayPaymentDescription = $gatewayPaymentDescription;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

    public function getPaymentDescription(): string
    {
        return $this->paymentDescription;
    }

    public function setPaymentDescription(string $paymentDescription): void
    {
        $this->paymentDescription = $paymentDescription;
    }
}
