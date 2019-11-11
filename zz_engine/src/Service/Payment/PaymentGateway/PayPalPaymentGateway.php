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
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use Omnipay\Common\GatewayInterface;
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
            $this->logger->info('[payment][paypal] payment created response', [
                'responseData' => $data,
            ]);

            if ($response->isSuccessful()) {
                $paymentDto->setGatewayPaymentId($data['id']);
                $paymentDto->setGatewayToken($data['id']);
                $paymentDto->setGatewayStatus($data['state']);
                if ($response->isRedirect()) {
                    $paymentDto->setPaymentExecuteUrl($response->getRedirectUrl());
                }

                return;
            }

            $this->logger->critical('[payment][paypal] payment creation not successful', [
                'data' => $data,
            ]);

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later');
        } catch (\Exception $e) {
            $this->logger->critical('[payment][paypal] error while payment creation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto
    {
        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $paymentId = $confirmPaymentConfigDto->getRequest()->get('paymentId');
            $payerId = $confirmPaymentConfigDto->getRequest()->get('PayerID');

            $gateway = $this->getGateway();
            $transaction = $gateway->completePurchase([
                'payer_id' => $payerId,
                'transactionReference' => $paymentId,
            ]);
            $response = $transaction->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                $this->logger->info('[payment][paypal] payment confirmation response', [
                    'responseData' => $data,
                ]);

                $confirmPaymentDto->setGatewayTransactionId($data['id']);
                $confirmPaymentDto->setGatewayPaymentId($data['id']);
                $confirmPaymentDto->setGatewayStatus($data['state']);
                $confirmPaymentDto->setConfirmed($response->isSuccessful());
                $confirmPaymentDto->setGatewayAmount(Integer::toInteger($data['transactions'][0]['amount']['total'] * 100));

                return $confirmPaymentDto;
            }

            $this->logger->critical('[payment][paypal] payment confirmation not successful', [
                'responseData' => $response->getData(),
            ]);

            throw UserVisibleException::fromPrevious('trans.Payment confirmation failed');
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][paypal] error during payment confirmation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Error during payment confirmation', $e);
        }
    }

    private function getGateway(): GatewayInterface
    {
        $gateway = Omnipay::create('PayPal_Rest');

        // sandbox / demo, client id and secret
        // client id: AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS
        // client secret: EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL
        $gateway->initialize([
            'clientId' => $this->settingsService->getSettingsDto()->getPaymentPayPalClientId(),
            'secret' => $this->settingsService->getSettingsDto()->getPaymentPayPalClientSecret(),
            'testMode' => true, // Or false when you are ready for live transactions
        ]);

        return $gateway;
    }

    public static function getName(): string
    {
        return PaymentGatewayEnum::PAYPAL;
    }
}