<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Payment\Dto\PaymentDto;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentHelperService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getSuccessUrl(PaymentDto $paymentDto): string
    {
        return $this->urlGenerator->generate('app_payment', [
            'paymentAppToken' => $paymentDto->getPaymentAppToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getPaymentWaitUrl(PaymentDto $paymentDto): string
    {
        return $this->urlGenerator->generate('app_payment_wait', [
            'paymentAppToken' => $paymentDto->getPaymentAppToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getPaymentNotifyUrl(PaymentDto $paymentDto): string
    {
        return $this->urlGenerator->generate('app_payment_notify', [
            'paymentAppToken' => $paymentDto->getPaymentAppToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getCancelUrl(PaymentDto $paymentDto): string
    {
        return $this->urlGenerator->generate('app_payment_cancel', [
            'paymentAppToken' => $paymentDto->getPaymentAppToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
