<?php

declare(strict_types=1);

namespace App\Service\Payment;

class ConfirmPaymentDto
{
    /**
     * @var string|null
     */
    public $gatewayTransactionId;

    /**
     * @var string|null
     */
    private $gatewayStatus;

    /**
     * @var boolean
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

    public function getGatewayTransactionId(): ?string
    {
        return $this->gatewayTransactionId;
    }

    public function setGatewayTransactionId(?string $gatewayTransactionId): void
    {
        $this->gatewayTransactionId = $gatewayTransactionId;
    }
}
