<?php

declare(strict_types=1);

namespace App\Service\Payment\PaymentGateway;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\FilePath;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Payment\Dto\ConfirmPaymentConfigDto;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Enum\PaymentGatewayEnum;
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Payment\Enum\GatewayModeEnum;
use App\Service\Payment\PaymentHelperService;
use App\Service\Setting\SettingsService;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Psr\Log\LoggerInterface;

class PayPalNativePaymentGateway implements PaymentGatewayInterface
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
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->paymentHelperService->getSuccessUrl($paymentDto));
        $redirectUrls->setCancelUrl($this->paymentHelperService->getCancelUrl($paymentDto));

        $amount = new Amount();
        $amount->setCurrency($paymentDto->getCurrency());
        $amount->setTotal($paymentDto->getAmount() / 100);

        $item = new Item();
        $item->setName($paymentDto->getGatewayPaymentDescription());
        $item->setPrice($amount->getTotal());
        $item->setCurrency($paymentDto->getCurrency());
        $item->setQuantity(1);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);

        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {
            $payment->create($this->getApiContext());
            $approvalUrl = $payment->getApprovalLink();

            $paymentDto->setPaymentExecuteUrl($approvalUrl);
            $paymentDto->setGatewayPaymentId($payment->getId());
            $paymentDto->setGatewayToken($payment->getToken());
            $paymentDto->setGatewayStatus($payment->getState());

            // Redirect the customer to $approvalUrl
        } catch (\Exception $e) {
            $this->logger->critical('[payment][paypal native] error while payment creation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentConfigDto $confirmPaymentConfigDto): ConfirmPaymentDto
    {
        $confirmPaymentDto = new ConfirmPaymentDto();
        $paymentId = $confirmPaymentConfigDto->getRequest()->get('paymentId');
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($confirmPaymentConfigDto->getRequest()->get('PayerID'));

        try {
            $payment = $payment->execute($execution, $apiContext);

            $confirmPaymentDto->setGatewayTransactionId($payment->getTransactions()[0]->getRelatedResources()[0]->getSale()->getId());
            $confirmPaymentDto->setGatewayPaymentId($payment->getId());
            $confirmPaymentDto->setGatewayStatus($payment->getState());
            $confirmPaymentDto->setConfirmed($payment->getState() === 'approved');
            $confirmPaymentDto->setGatewayAmount(Integer::toInteger($payment->getTransactions()[0]->getAmount()->getTotal() * 100));

            return $confirmPaymentDto;
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][paypal native] failed to confirm PayPal payment', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Payment confirmation failed, if you have been charged and did not receive service, please contact us', $e);
        }
    }

    private function getApiContext(): ApiContext
    {
        // sandbox / demo, client id and secret
        // client id: AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS
        // client secret: EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientId(),
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientSecret(),
            )
        );
        $apiContext->setConfig([
            'mode' => $this->getGatewayMode(),
            'log.LogEnabled' => true,
            'log.FileName' => FilePath::getLogDir() . '/payPal_'. \date('Y-m') .'.log',
            'log.LogLevel' => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS, DEBUG in dev only
            'cache.enabled' => true,
            'cache.FileName' => FilePath::getCacheDir() . '/payPalCache.php', // for determining paypal cache directory
            'http.CURLOPT_CONNECTTIMEOUT' => 20,
        ]);

        return $apiContext;
    }

    public function getGatewayMode(): string
    {
        return $this->settingsService->getSettingsDto()->getPaymentPayPalMode() ?? GatewayModeEnum::SANDBOX;
    }

    public static function getName(): string
    {
        return PaymentGatewayEnum::PAYPAL_NATIVE;
    }
}