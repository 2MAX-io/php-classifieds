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
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class OmnipayPaymentMethod implements PaymentMethodInterface
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
                'returnUrl' => $this->paymentHelperService->getSuccessUrl(),
                'cancelUrl' => $this->paymentHelperService->getCancelUrl(),
            ]);
            $response = $transaction->send();
            $data = $response->getData();

            $paymentDto->setGatewayPaymentId($data["id"]);
            $paymentDto->setGatewayToken('todo');
            $paymentDto->setGatewayStatus($data["state"]);

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
}