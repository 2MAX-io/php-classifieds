<?php

declare(strict_types=1);

namespace App\Service\Payment\PaymentGateway;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\IntegerHelper;
use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Payment\Enum\GatewayModeEnum;
use App\Service\Payment\Enum\PaymentGatewayEnum;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Omnipay;
use Psr\Log\LoggerInterface;

class PayPalPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var PaymentHelperService
     */
    private $paymentHelperService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PaymentHelperService $paymentHelperService,
        SettingsService $settingsService,
        LoggerInterface $logger
    ) {
        $this->paymentHelperService = $paymentHelperService;
        $this->settingsService = $settingsService;
        $this->logger = $logger;
    }

    public static function getName(): string
    {
        return PaymentGatewayEnum::PAYPAL;
    }

    public function createPayment(PaymentDto $paymentDto): void
    {
        try {
            $gateway = $this->getGateway();
            $transaction = $gateway->purchase([
                'amount' => $paymentDto->getAmount() / 100,
                'currency' => $paymentDto->getCurrency(),
                'description' => $paymentDto->getGatewayPaymentDescription(),
                'returnUrl' => $this->paymentHelperService->getSuccessUrl($paymentDto),
                'cancelUrl' => $this->paymentHelperService->getCancelUrl($paymentDto),
            ]);
            $response = $transaction->send();
            $data = $response->getData();
            $this->logger->debug('[payment][paypal] payment created response', [
                'responseData' => $data,
            ]);

            if (!$response->isSuccessful()) {
                $this->logger->critical('[payment][paypal] payment creation failed', [
                    'data' => $data,
                ]);

                throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later');
            }

            $paymentDto->setGatewayPaymentId($data['id']);
            $paymentDto->setGatewayStatus($data['state']);
            if ($response instanceof RedirectResponseInterface && $response->isRedirect()) {
                $paymentDto->setMakePaymentUrl($response->getRedirectUrl());
            }
        } catch (\Exception $e) {
            $this->logger->critical('[payment][paypal] error while payment creation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error while creating payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        try {
            $paymentId = $confirmPaymentDto->getRequest()->get('paymentId');
            $payerId = $confirmPaymentDto->getRequest()->get('PayerID');

            $gateway = $this->getGateway();
            $transaction = $gateway->completePurchase([
                'payer_id' => $payerId,
                'transactionReference' => $paymentId,
            ]);
            $response = $transaction->send();
            if (!$response->isSuccessful()) {
                $this->logger->critical('[payment][paypal] payment confirmation failed', [
                    'responseData' => $response->getData(),
                ]);

                throw UserVisibleException::fromPrevious('trans.Payment confirmation failed, if you have been charged and did not receive service, please contact us');
            }
            $data = $response->getData();
            $this->logger->debug('[payment][paypal] payment confirmation response', [
                'responseData' => $data,
            ]);

            $confirmPaymentDto->setGatewayPaymentId($data['id']);
            $confirmPaymentDto->setGatewayStatus($data['state']);
            $confirmPaymentDto->setConfirmed($response->isSuccessful());
            $confirmPaymentDto->setGatewayAmount(IntegerHelper::toInteger($data['transactions'][0]['amount']['total'] * 100));

            return $confirmPaymentDto;
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][paypal] error during payment confirmation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error during payment confirmation, if you have been charged and did not receive service, please contact us', $e);
        }
    }

    public function getGatewayMode(): string
    {
        $paymentPayPalMode = $this->settingsService->getSettingsDto()->getPaymentPayPalMode();
        if (null === $paymentPayPalMode) {
            throw new \RuntimeException('PaymentPayPalMode is null');
        }

        return $paymentPayPalMode;
    }

    private function getGateway(): GatewayInterface
    {
        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->initialize([
            'clientId' => $this->settingsService->getSettingsDto()->getPaymentPayPalClientId(),
            'secret' => $this->settingsService->getSettingsDto()->getPaymentPayPalClientSecret(),
            'testMode' => GatewayModeEnum::SANDBOX === $this->getGatewayMode(),
        ]);

        return $gateway;
    }
}
