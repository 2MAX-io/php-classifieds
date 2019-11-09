<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;

class ConfirmPaymentConfigDto
{
    /**
     * @var Request
     */
    public $request;

    /**
     * @var Payment
     */
    public $paymentEntity;

    /**
     * @var string
     */
    public $paymentAppToken;

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getPaymentAppToken(): string
    {
        return $this->paymentAppToken;
    }

    public function setPaymentAppToken(string $paymentAppToken): void
    {
        $this->paymentAppToken = $paymentAppToken;
    }

    public function getPaymentEntity(): Payment
    {
        return $this->paymentEntity;
    }

    public function setPaymentEntity(Payment $paymentEntity): void
    {
        $this->paymentEntity = $paymentEntity;
    }
}
