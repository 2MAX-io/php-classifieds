<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Payment\Paypal;

use App\Service\Payment\PaymentGateway\PayPalPaymentGatewayService;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\PayPal\RestGateway;

/**
 * @internal
 */
class TopUpTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        // go to top up page
        $crawler = $client->request('GET', $this->getRouter()->generate('app_user_balance_top_up'));
        $getFormResponse = $client->getResponse();

        // prepare services state
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->getCookieJar()->updateFromSetCookie([(string) $getFormResponse->headers->get('set-cookie')]);
        $gatewayStub = $this->createMock(RestGateway::class);
        $gatewayStub->expects(self::once())->method('purchase')->willReturnCallback(function (array $data) {
            $responseStub = $this->createMock(RedirectResponseInterface::class);
            $responseStub->expects(self::once())->method('getData')->willReturn([
                'id' => 'TEST-PAYMENT_ID',
                'state' => 'created',
            ]);
            $responseStub->method('isSuccessful')->willReturn(true);
            $responseStub->method('isRedirect')->willReturn(true);
            $responseStub->method('getRedirectUrl')->willReturn($data['returnUrl']);
            $requestStub = $this->createMock(RequestInterface::class);
            $requestStub->expects(self::once())->method('send')->willReturn($responseStub);

            return $requestStub;
        });
        self::$container->get(PayPalPaymentGatewayService::class)->setGateway($gatewayStub);

        // submit top up form
        $buttonNode = $crawler->selectButton('Top up account');
        $form = $buttonNode->form([
            'top_up_balance[topUpAmount]' => '1',
        ], 'POST');
        $client->submit($form);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // go to success url
        $successUrl = $client->getResponse()->headers->get('location');
        self::ensureKernelShutdown();
        $client = static::createClient();
        $this->loginUser($client);
        $gatewayStub->expects(self::once())->method('completePurchase')->willReturnCallback(function () {
            $responseStub = $this->createMock(RedirectResponseInterface::class);
            $responseStub->expects(self::once())->method('getData')->willReturn([
                'id' => 'TEST-PAYMENT_ID',
                'state' => 'success',
                'transactions' => [
                    0 => [
                        'amount' => [
                            'total' => 1,
                        ],
                    ],
                ],
            ]);
            $responseStub->method('isSuccessful')->willReturn(true);
            $requestStub = $this->createMock(RequestInterface::class);
            $requestStub->expects(self::once())->method('send')->willReturn($responseStub);

            return $requestStub;
        });
        $this->getTestContainer()->get(PayPalPaymentGatewayService::class)->setGateway($gatewayStub);
        self::$container->get(PayPalPaymentGatewayService::class)->setGateway($gatewayStub);

        $client->request('GET', $successUrl);
        self::assertEquals('app_payment_gateway_success', $client->getRequest()->get('_route'));
        $client->followRedirect();
        self::assertStringContainsString('app_user_balance_top_up', $client->getRequest()->get('_route'));
    }
}
