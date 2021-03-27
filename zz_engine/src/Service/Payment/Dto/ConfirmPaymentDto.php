<?php

declare(strict_types=1);

namespace App\Service\Payment\Dto;

use App\Entity\Payment;

class ConfirmPaymentDto
{
    /**
     * @var string|null
     */
    public $gatewayPaymentId;

    /**
     * @var string|null
     */
    private $gatewayStatus;

    /**
     * @var int|null
     */
    private $gatewayAmount;

    /**
     * @var Payment|null
     */
    private $paymentEntity;

    /**
     * @var bool
     */
    private $confirmed = false;

    public function getGatewayStatus(): ?string
    {
        return $this->gatewayStatus;
    }

    public function setGatewayStatus(?string $gatewayStatus): void
    {
        $this->gatewayStatus = $gatewayStatus;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    public function getGatewayAmount(): ?int
    {
        return $this->gatewayAmount;
    }

    public function setGatewayAmount(?int $gatewayAmount): void
    {
        $this->gatewayAmount = $gatewayAmount;
    }

    public function getGatewayPaymentId(): ?string
    {
        return $this->gatewayPaymentId;
    }

    public function setGatewayPaymentId(?string $gatewayPaymentId): void
    {
        $this->gatewayPaymentId = $gatewayPaymentId;
    }

    public function getPaymentEntity(): ?Payment
    {
        return $this->paymentEntity;
    }

    public function getPaymentEntityNotNull(): Payment
    {
        if (null === $this->paymentEntity) {
            throw new \RuntimeException('paymentEntity is null');
        }

        return $this->paymentEntity;
    }

    public function setPaymentEntity(?Payment $paymentEntity): void
    {
        $this->paymentEntity = $paymentEntity;
    }
}
