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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class MakeFeaturedTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

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
        $this->getTestContainer()->get(PayPalPaymentGatewayService::class)->setGateway($gatewayStub);

        $id = 1;
        $url = $this->getRouter()->generate('app_user_feature_listing_action', [
            'id' => $id,
            'package' => 1,
        ]);
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_feature'.$id);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());

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

        $client->request('GET', $successUrl);
        self::assertSame('app_payment_gateway_success', $client->getRequest()->get('_route'));
        $client->followRedirect();
        self::assertStringContainsString('app_user_feature_listing', $client->getRequest()->get('_route'));
    }
}
