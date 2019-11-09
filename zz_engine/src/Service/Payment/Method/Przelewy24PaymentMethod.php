<?php
declare(strict_types=1);

namespace App\Service\Payment\Method;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentMethodInterface;
use App\Service\Payment\ConfirmPaymentConfigDto;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Omnipay\Przelewy24\Gateway;
use Psr\Log\LoggerInterface;

class Przelewy24PaymentMethod implements PaymentMethodInterface
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
                'currency' => 'PLN',
                'description' => $paymentDto->getGatewayPaymentDescription(),
                'returnUrl' => $this->paymentHelperService->getPaymentWaitUrl($paymentDto),
                'notifyUrl' => $this->paymentHelperService->getPaymentNotifyUrl($paymentDto),
                'cancelUrl' => $this->paymentHelperService->getCancelUrl($paymentDto),
                'card' => [
                    'email' => 'info@example.com',
                    'name' => 'My name',
                    'country' => 'PL',
                ],
            ]);
            $response = $transaction->send();
            $data = $response->getData();

            $paymentDto->setGatewayPaymentId($data['token']);
            $paymentDto->setGatewayToken($data['token']);
            $paymentDto->setGatewayStatus($data['error']);

            if ($response->isSuccessful()) {
//                echo "Step 2 was successful!\n";
            }

            if ($response->isRedirect()) {
                $paymentDto->setPaymentExecuteUrl($response->getRedirectUrl());
            }
        } catch (\Exception $e) {
            $this->logger->critical('error while createPayment', ExceptionHelper::flatten($e)); // todo

            throw new UserVisibleException('can not create payment');
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
                'currency' => 'PLN',
                'transactionId' => $transactionId,
            ]);
            $response = $transaction->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                $this->logger->info('confirm payment response', [
                    'responseData' => $data,
                ]);
                $confirmPaymentDto->setGatewayTransactionId($transactionId);
                $confirmPaymentDto->setGatewayStatus($data['error']);
                $confirmPaymentDto->setConfirmed($response->isSuccessful());
                $confirmPaymentDto->setGatewayAmount(Integer::toInteger($confirmPaymentConfigDto->getRequest()->get('p24_amount')));

                return $confirmPaymentDto;
            } else {
                $this->logger->critical('payment not successful', [
                    'responseData' => $response->getData(),
                ]);

                throw new UserVisibleException('Payment not successful'); //todo
            }
        } catch (\Throwable $e) {
            $this->logger->critical('error while confirmPayment', ExceptionHelper::flatten($e));

            throw new UserVisibleException('Payment confirmation failed', [], 0, $e); //todo
        }
    }

    private function getGateway(): GatewayInterface
    {
        $gateway = Omnipay::create('Przelewy24');

        $gateway->initialize([
            'merchantId' => '88765',
            'posId'      => '88765',
            'crc'        => '46c6913c10c95dd9',
            'testMode'   => true,
        ]);

        return $gateway;
    }
}