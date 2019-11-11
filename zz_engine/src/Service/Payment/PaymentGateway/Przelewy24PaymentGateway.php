<?php
declare(strict_types=1);

namespace App\Service\Payment\PaymentGateway;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Payment\Dto\ConfirmPaymentConfigDto;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Enum\PaymentGatewayEnum;
use App\Service\Payment\Enum\PaymentModeEnum;
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Omnipay\Przelewy24\Gateway;
use Psr\Log\LoggerInterface;

class Przelewy24PaymentGateway implements PaymentGatewayInterface
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
                    'email' => $paymentDto->getUser()->getEmail(),
                ],
            ]);
            $response = $transaction->send();
            $data = $response->getData();
            $this->logger->info('[payment][przelewy24] payment created response', [
                'responseData' => $data,
            ]);

            if ($response->isSuccessful()) {
                $paymentDto->setGatewayPaymentId($data['token']);
                $paymentDto->setGatewayToken($data['token']);
                $paymentDto->setGatewayStatus($data['error']);
                if ($response->isRedirect()) {
                    $paymentDto->setPaymentExecuteUrl($response->getRedirectUrl());
                }

                return;
            }

            $this->logger->critical('[payment][przelewy24] payment creation not successful', [
                'data' => $data,
            ]);

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later');
        } catch (\Exception $e) {
            $this->logger->critical('[payment][przelewy24] error while creating payment', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto
    {
        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $gateway = $this->getGateway();
            $transactionId = $confirmPaymentConfigDto->getRequest()->get('p24_order_id');
            $transaction = $gateway->completePurchase([
                'sessionId' => $confirmPaymentConfigDto->getPaymentAppToken(),
                'amount' => $confirmPaymentConfigDto->getPaymentEntity()->getAmount() / 100,
                'currency' => $confirmPaymentConfigDto->getPaymentEntity()->getCurrency(),
                'transactionId' => $transactionId,
            ]);
            $response = $transaction->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                $this->logger->info('[payment] payment confirmation response', [
                    'responseData' => $data,
                ]);

                $confirmPaymentDto->setGatewayTransactionId($transactionId);
                $confirmPaymentDto->setGatewayStatus($data['error']);
                $confirmPaymentDto->setConfirmed($response->isSuccessful());
                $confirmPaymentDto->setGatewayAmount(Integer::toInteger($confirmPaymentConfigDto->getRequest()->get('p24_amount')));

                return $confirmPaymentDto;
            }

            $this->logger->critical('[payment][przelewy24] payment confirmation not successful', [
                'responseData' => $response->getData(),
            ]);

            throw new UserVisibleException('trans.Payment confirmation failed');
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][przelewy24] error while payment confirmation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error during payment confirmation', $e);
        }
    }

    private function getGateway(): GatewayInterface
    {
        $gateway = Omnipay::create('Przelewy24');

        $gateway->initialize([
            'merchantId' => $this->settingsService->getPaymentPrzelewy24MerchantId(),
            'posId'      => $this->settingsService->getPaymentPrzelewy24PosId(),
            'crc'        => $this->settingsService->getPaymentPrzelewy24Crc(),
            'testMode'   => $this->settingsService->getPaymentPrzelewy24Mode() === PaymentModeEnum::SANDBOX,
        ]);

        return $gateway;
    }

    public static function getName(): string
    {
        return PaymentGatewayEnum::PRZELEWY24;
    }
}