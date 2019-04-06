<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Setting\SettingsDto;
use Psr\Log\LoggerInterface;

class PaymentGatewayService
{
    /**
     * @var iterable|PaymentGatewayInterface[]
     */
    private $paymentMethodList;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param iterable|PaymentGatewayInterface[] $paymentMethodList
     */
    public function __construct(iterable $paymentMethodList, SettingsDto $settingsDto, LoggerInterface $logger)
    {
        $this->paymentMethodList = $paymentMethodList;
        $this->settingsDto = $settingsDto;
        $this->logger = $logger;
    }

    public function getPaymentGateway(): PaymentGatewayInterface
    {
        $paymentGatewayName = $this->settingsDto->getPaymentGateway();
        foreach ($this->paymentMethodList as $paymentMethod) {
            if ($paymentMethod::getName() === $paymentGatewayName) {
                $this->logger->debug('using payment gateway: {paymentGateway}', [
                    'paymentGateway' => $paymentGatewayName,
                ]);

                return $paymentMethod;
            }
        }

        throw new \RuntimeException("payment gateway not found: `{$paymentGatewayName}`");
    }
}
