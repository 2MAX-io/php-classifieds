<?php

declare(strict_types=1);

namespace App\Service\Payment\Method;

use App\Exception\UserVisibleMessageException;
use App\Helper\FilePath;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentMethodInterface;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;
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
use Symfony\Component\HttpFoundation\Request;

class PayPalPaymentMethod implements PaymentMethodInterface
{
    /**
     * @var PaymentHelperService
     */
    private $paymentHelperService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        PaymentHelperService $paymentHelperService,
        SettingsService $settingsService
    ) {
        $this->paymentHelperService = $paymentHelperService;
        $this->settingsService = $settingsService;
    }

    public function createPayment(PaymentDto $paymentDto): void
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->paymentHelperService->getSuccessUrl());
        $redirectUrls->setCancelUrl($this->paymentHelperService->getCancelUrl());

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
        } catch (\Exception $ex) {
            throw new UserVisibleMessageException('trans.Failed to create payment, please try again later', [], 0, $ex);
        }
    }

    public function confirmPayment(Request $request, ConfirmPaymentDto $confirmPaymentDto): ConfirmPaymentDto
    {
        $paymentId = $request->get('paymentId'); // todo: pass this in dto
        $apiContext = $this->getApiContext();
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID')); // todo: pass this in dto

        try {
            $payment = $payment->execute($execution, $apiContext);

            $confirmPaymentDto->setGatewayTransactionId($payment->getTransactions()[0]->getRelatedResources()[0]->getSale()->getId());
            $confirmPaymentDto->setGatewayPaymentId($payment->getId());
            $confirmPaymentDto->setGatewayStatus($payment->getState());
            $confirmPaymentDto->setConfirmed($payment->getState() === 'approved');
            $confirmPaymentDto->setGatewayAmount(Integer::toInteger($payment->getTransactions()[0]->getAmount()->getTotal() * 100));

            return $confirmPaymentDto;

        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function getApiContext(): ApiContext
    {
        // todo: make this method production worthy

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientId(),
                $this->settingsService->getSettingsDto()->getPaymentPayPalClientSecret()
            )
        );
        $apiContext->setConfig(
            array(
                'mode' => $this->settingsService->getSettingsDto()->getPaymentPayPalMode() ?? 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => FilePath::getLogDir() . '/payPal_'. date('Y-m') .'.log',
                'log.LogLevel' => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS, DEBUG in dev only
                'cache.enabled' => true,
                'cache.FileName' => FilePath::getCacheDir() . '/payPalCache.php', // for determining paypal cache directory
                'http.CURLOPT_CONNECTTIMEOUT' => 30,
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        return $apiContext;
    }
}
