<?php
declare(strict_types=1);

namespace App\Service\Payment\Method;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentMethodInterface;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Omnipay\Przelewy24\Gateway;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class OmnipayPrzelewy24PaymentMethod implements PaymentMethodInterface
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
                'sessionId' => 'unique',
                'amount' => $paymentDto->getAmount() / 100,
                'currency' => $paymentDto->getCurrency(),
                'currency' => 'PLN',
                'description' => $paymentDto->getGatewayPaymentDescription(),
                'returnUrl' => $this->paymentHelperService->getSuccessUrl(),
                'notifyUrl' => $this->paymentHelperService->getSuccessUrl(),
                'cancelUrl' => $this->paymentHelperService->getCancelUrl(),
                'card' => [
                    'email' => 'info@example.com',
                    'name' => 'My name',
                    'country' => 'PL',
                ],
            ]);
            $response = $transaction->send();
            $data = $response->getData();

            $paymentDto->setGatewayPaymentId($data['token']);
            $paymentDto->setGatewayToken('token');
            $paymentDto->setGatewayStatus($data['error']);

            if ($response->isSuccessful()) {
//                echo "Step 2 was successful!\n";
            }

            if ($response->isRedirect()) {
                $paymentDto->setPaymentExecuteUrl($response->getRedirectUrl());
            }
        } catch (\Exception $e) {
//            echo "Exception caught while attempting purchase.\n";
//            echo "Exception type == " . get_class($e) . "\n";
//            echo "Message == " . $e->getMessage() . "\n";

            $this->logger->critical('error while createPayment', ExceptionHelper::flatten($e)); // todo
        }
    }

    public function confirmPayment(Request $request): ConfirmPaymentDto
    {
        try {
            $confirmPaymentDto = new ConfirmPaymentDto();
            $paymentId = $request->get('paymentId');
            $payerId = $request->get('PayerID');

            $gateway = $this->getGateway();
            $transaction = $gateway->completePurchase([
                'payer_id' => $payerId,
                'transactionReference' => $paymentId,
            ]);
            $response = $transaction->send();
            if ($response->isSuccessful()) {
                $data = $response->getData();
                $confirmPaymentDto->setGatewayTransactionId($data['id']);
                $confirmPaymentDto->setGatewayPaymentId($data['id']);
                $confirmPaymentDto->setGatewayStatus($data['state']);
                $confirmPaymentDto->setConfirmed($response->isSuccessful());
                $confirmPaymentDto->setGatewayAmount(Integer::toInteger($data['transactions'][0]['amount']['total'] * 100));

                return $confirmPaymentDto;
            } else {
                throw new UserVisibleException('Payment confirmation failed'); //todo
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