<?php

declare(strict_types=1);

namespace App\Service\Payment\PaymentGateway;

use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
use App\Helper\ExceptionHelper;
use App\Helper\FilePath;
use App\Helper\IntegerHelper;
use App\Service\Payment\Base\PaymentGatewayInterface;
use App\Service\Payment\Dto\ConfirmPaymentDto;
use App\Service\Payment\Dto\PaymentDto;
use App\Service\Payment\Enum\PaymentGatewayEnum;
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

    public static function getName(): string
    {
        return PaymentGatewayEnum::PAYPAL_NATIVE;
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
        $item->setQuantity('1');

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);

        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions([$transaction]);

        try {
            $payment->create($this->getApiContext());
            $approvalUrl = $payment->getApprovalLink();

            $paymentDto->setMakePaymentUrl($approvalUrl);
            $paymentDto->setGatewayPaymentId($payment->getId());
            $paymentDto->setGatewayStatus($payment->getState());

            // Redirect the customer to $approvalUrl
        } catch (\Exception $e) {
            $this->logger->critical('[payment][paypal native] error while payment creation', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Failed to create payment, please try again later', $e);
        }
    }

    public function confirmPayment(ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        $paymentId = $confirmPaymentDto->getRequest()->get('paymentId');
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($confirmPaymentDto->getRequest()->get('PayerID'));

        try {
            $payment = $payment->execute($execution, $apiContext);

            $confirmPaymentDto->setGatewayPaymentId($payment->getId());
            $confirmPaymentDto->setGatewayStatus($payment->getState());
            $confirmPaymentDto->setConfirmed('approved' === $payment->getState());
            $total = (float) $payment->getTransactions()[0]->getAmount()->getTotal();
            $confirmPaymentDto->setGatewayAmount(IntegerHelper::toInteger($total * 100));

            return $confirmPaymentDto;
        } catch (\Throwable $e) {
            $this->logger->critical('[payment][paypal native] failed to confirm PayPal payment', ExceptionHelper::flatten($e));

            throw UserVisibleException::fromPrevious('trans.Payment confirmation failed, if you have been charged and did not receive service, please contact us', $e);
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

    private function getApiContext(): ApiContext
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientId(),
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientSecret(),
            )
        );
        $apiContext->setConfig([
            'mode' => $this->getGatewayMode(),
            'log.LogEnabled' => true,
            'log.FileName' => FilePath::getLogDir().'/payPal_'.DateHelper::date('Y-m').'.log',
            'log.LogLevel' => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS, DEBUG in dev only
            'cache.enabled' => true,
            'cache.FileName' => FilePath::getCacheDir().'/payPalCache.php', // for determining paypal cache directory
            'http.CURLOPT_CONNECTTIMEOUT' => 20,
        ]);

        return $apiContext;
    }
}
