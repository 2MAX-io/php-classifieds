<?php
declare(strict_types=1);

namespace App\Service\Payment;

use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Setting\SettingsService;
use Psr\Log\LoggerInterface;

class PaymentGatewayService
{
    /**
     * @var iterable|PaymentGatewayInterface[]
     */
    private $paymentMethodList;
    /**
     * @var SettingsService
     */
    private $settingsService;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(iterable $paymentMethodList, SettingsService $settingsService, LoggerInterface $logger)
    {
        $this->paymentMethodList = $paymentMethodList;
        $this->settingsService = $settingsService;
        $this->logger = $logger;
    }

    public function getPaymentGateway(): PaymentGatewayInterface
    {
        $paymentGatewayName = $this->settingsService->getPaymentGateway();
        foreach ($this->paymentMethodList as $paymentMethod) {
            if ($paymentMethod::getName() === $paymentGatewayName) {
                $this->logger->debug('using payment gateway: {paymentGateway}', [
                    'paymentGateway' => $paymentGatewayName,
                ]);

                return $paymentMethod;
            }
        }

        throw new \RuntimeException("payment gateway not found: $paymentGatewayName");
    }
}