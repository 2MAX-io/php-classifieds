<?php

declare(strict_types=1);

namespace App\Service\Payment\Method;

use App\Helper\FilePath;
use App\Helper\Integer;
use App\Service\Payment\Base\PaymentMethodInterface;
use App\Service\Payment\ConfirmPaymentDto;
use App\Service\Payment\PaymentDto;
use App\Service\Payment\PaymentHelperService;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Symfony\Component\HttpFoundation\Request;

class PayPalPaymentMethod implements PaymentMethodInterface
{
    /**
     * @var PaymentHelperService
     */
    private $paymentHelperService;

    public function __construct(PaymentHelperService $paymentHelperService)
    {
        $this->paymentHelperService = $paymentHelperService;
    }

    public function createPayment(PaymentDto $paymentDto)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal"); // todo: make sure right

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->paymentHelperService->getSuccessUrl());
        $redirectUrls->setCancelUrl($paymentDto->getGatewayCancelUrl()); // todo: set correctly

        $amount = new Amount();
        $amount->setCurrency($paymentDto->getCurrency());
        $amount->setTotal($paymentDto->getAmount() / 100);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription("Payment description"); // todo: set to something

        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {
            $payment->create($this->getApiContext());
            $approvalUrl = $payment->getApprovalLink();

            $paymentDto->setPaymentExecuteUrl($approvalUrl);
            $paymentDto->setGatewayTransactionId($payment->getId());
            $paymentDto->setGatewayToken($payment->getToken());
            $paymentDto->setGatewayStatus($payment->getState());

            // Redirect the customer to $approvalUrl
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData(); // todo: handle exception better
            die($ex);
        } catch (\Exception $ex) {
            die($ex); // todo: handle exception better
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

            $confirmPaymentDto->setGatewayTransactionId($payment->getId());
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
        $clientId = 'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS';
        $clientSecret = 'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL';

        // todo: make this method production worthy

        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */
        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );
        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox', // todo: set correctly for production
                'log.LogEnabled' => true,
                'log.FileName' => FilePath::getLogDir() . '/payPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                //'cache.FileName' => '/PaypalCache' // for determining paypal cache directory
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );
        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');
        return $apiContext;
    }
}
