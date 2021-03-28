<?php

declare(strict_types=1);

namespace App\Service\Payment\Dto;

use App\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfirmPaymentDto
{
    /**
     * @var Request
     */
    public $request;

    /**
     * @var string
     */
    public $paymentAppToken;

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

    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var Response|null
     */
    private $redirectResponse;

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

    public function getRedirectResponse(): ?Response
    {
        return $this->redirectResponse;
    }

    public function getRedirectResponseNotNull(): Response
    {
        if (null === $this->redirectResponse) {
            throw new \RuntimeException('redirectResponse is null');
        }

        return $this->redirectResponse;
    }

    public function setRedirectResponse(?Response $redirectResponse): void
    {
        $this->redirectResponse = $redirectResponse;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }
}
