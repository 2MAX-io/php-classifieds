<?php

declare(strict_types=1);

namespace App\Service\Payment\Dto;

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
    private $makePaymentUrl;

    /**
     * @var int|null
     */
    private $amount;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $gatewayPaymentId;

    /**
     * @var string|null
     */
    private $gatewayStatus;

    /**
     * @var string|null
     */
    private $gatewayPaymentDescription;

    /**
     * @var string|null
     */
    private $paymentAppToken;

    /**
     * @var Payment|null
     */
    private $paymentEntity;

    /**
     * @var User|null
     */
    private $user;

    public function setMakePaymentUrl(string $makePaymentUrl): void
    {
        $this->makePaymentUrl = $makePaymentUrl;
    }

    public function getMakePaymentUrl(): string
    {
        return $this->makePaymentUrl;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserNotNull(): User
    {
        if (null === $this->user) {
            throw new \RuntimeException('user is null');
        }

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

    public function getPaymentAppToken(): ?string
    {
        return $this->paymentAppToken;
    }

    public function setPaymentAppToken(?string $paymentAppToken): void
    {
        $this->paymentAppToken = $paymentAppToken;
    }
}
