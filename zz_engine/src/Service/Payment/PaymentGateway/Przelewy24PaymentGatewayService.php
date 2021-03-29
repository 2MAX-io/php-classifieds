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
use App\Service\Setting\SettingsDto;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Omnipay;
use Omnipay\Przelewy24\Gateway;
use Psr\Log\LoggerInterface;

class Przelewy24PaymentGatewayService implements PaymentGatewayInterface
{
    /**
     * @var PaymentHelperService
     */
    private $paymentHelperService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PaymentHelperService $paymentHelperService,
        SettingsDto $settingsDto,
        LoggerInterface $logger
    ) {
        $this->paymentHelperService = $paymentHelperService;
        $this->settingsDto = $settingsDto;
        $this->logger = $logger;
    }

    public static function getName(): string
    {
        return PaymentGatewayEnum::PRZELEWY24;
    }

    public function createPayment(PaymentDto $paymentDto): void
    {
        try {
            $gateway = $this->getGateway();
            $transaction = $gateway->purchase([
                'channel' => Gateway::P24_CHANNEL_ALL,
                'sessionId' => $paymentDto->getPaymentAppToken(),
                'amount' => $paymentDto->getAmount() / 100,
                'currency' => $paymentDto->getCurrency(),
                'description' => $paymentDto->getGatewayPaymentDescription(),
                'returnUrl' => $this->paymentHelperService->getPaymentWaitUrl($paymentDto),
                'notifyUrl' => $this->paymentHelperService->getPaymentNotifyUrl($paymentDto),
                'cancelUrl' => $this->paymentHelperService->getCancelUrl($paymentDto),
                'card' => [
                    'email' => $paymentDto->getUserNotNull()->getEmail(),
                ],
            ]);
            $response = $transaction->send();
            $data = $response->getData();
            $this->logger->debug('[payment][przelewy24] payment created response', [
                'responseData' => $data,
            ]);

            if (!$response->isSuccessful()) {
                $this->logger->critical('[payment][przelewy24] payment creation failed', [
                    'data' => $data,
                ]);

                throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later');
            }

            $paymentDto->setGatewayStatus($data['error']);
            if ($response instanceof RedirectResponseInterface && $response->isRedirect()) {
                $paymentDto->setMakePaymentUrl($response->getRedirectUrl());
            }
        } catch (\Exception $e) {
            $this->logger->critical('[payment][przelewy24] error while creating payment', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error while creating payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        try {
            $gateway = $this->getGateway();
            $transactionId = $confirmPaymentDto->getRequest()->get('p24_order_id');
            $transaction = $gateway->completePurchase([
                'sessionId' => $confirmPaymentDto->getPaymentAppToken(),
                'amount' => $confirmPaymentDto->getPaymentEntityNotNull()->getAmount() / 100,
                'currency' => $confirmPaymentDto->getPaymentEntityNotNull()->getCurrency(),
                'transactionId' => $transactionId,
            ]);
            $response = $transaction->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                $this->logger->debug('[payment] payment confirmation response', [
                    'responseData' => $data,
                    'query' => $confirmPaymentDto->getRequest()->request->all(),
                ]);

                $confirmPaymentDto->setGatewayPaymentId($transactionId);
                $confirmPaymentDto->setGatewayStatus($data['error']);
                $confirmPaymentDto->setConfirmed($response->isSuccessful());
                $confirmPaymentDto->setGatewayAmount(IntegerHelper::toInteger($confirmPaymentDto->getRequest()->get('p24_amount')));

                return $confirmPaymentDto;
            }

            $this->logger->critical('[payment][przelewy24] Payment confirmation failed', [
                'responseData' => $response->getData(),
            ]);

            throw new UserVisibleException('trans.Payment confirmation failed, if you have been charged and did not receive service, please contact us');
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][przelewy24] error while payment confirmation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error during payment confirmation, if you have been charged and did not receive service, please contact us', $e);
        }
    }

    public function getGatewayMode(): string
    {
        $paymentPrzelewy24Mode = $this->settingsDto->getPaymentPrzelewy24Mode();
        if (null === $paymentPrzelewy24Mode) {
            throw new \RuntimeException('getPaymentPrzelewy24Mode is null');
        }

        return $paymentPrzelewy24Mode;
    }

    private function getGateway(): GatewayInterface
    {
        $gateway = Omnipay::create('Przelewy24');
        $gateway->initialize([
            'merchantId' => $this->settingsDto->getPaymentPrzelewy24MerchantId(),
            'posId' => $this->settingsDto->getPaymentPrzelewy24PosId(),
            'crc' => $this->settingsDto->getPaymentPrzelewy24Crc(),
            'testMode' => GatewayModeEnum::SANDBOX === $this->settingsDto->getPaymentPrzelewy24Mode(),
        ]);

        return $gateway;
    }
}
