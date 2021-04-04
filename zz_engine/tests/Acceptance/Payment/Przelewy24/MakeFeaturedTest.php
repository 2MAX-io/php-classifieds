<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Payment\Przelewy24;

use App\Service\Payment\Enum\PaymentGatewayEnum;
use App\Service\Payment\PaymentGateway\Przelewy24PaymentGatewayService;
use App\Service\Setting\SettingsDto;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Przelewy24\Gateway;
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
        $this->getTestContainer()->get(SettingsDto::class)->setPaymentGateway(PaymentGatewayEnum::PRZELEWY24);
        $pdo = $this->getTestContainer()->get(EntityManagerInterface::class)->getConnection();
        $pdo->executeQuery("UPDATE setting SET value = :paymentGatewayName WHERE name = 'paymentGateway'", [
            ':paymentGatewayName' => PaymentGatewayEnum::PRZELEWY24,
        ]);
        $this->loginUser($client);

        $appPaymentToken = null;
        $gatewayStub = $this->createMock(Gateway::class);
        $gatewayStub->expects(self::once())->method('purchase')->willReturnCallback(function (array $data) use (&$appPaymentToken) {
            $appPaymentToken = $data['sessionId'];
            $responseStub = $this->createMock(RedirectResponseInterface::class);
            $responseStub->method('getData')->willReturn([
                'id' => 'TEST-PAYMENT_ID',
                'state' => 'created',
                'error' => '',
            ]);
            $responseStub->method('isSuccessful')->willReturn(true);
            $responseStub->method('isRedirect')->willReturn(true);
            $responseStub->method('getRedirectUrl')->willReturn($data['returnUrl']);
            $requestStub = $this->createMock(RequestInterface::class);
            $requestStub->method('send')->willReturn($responseStub);

            return $requestStub;
        });
        $this->getTestContainer()->get(Przelewy24PaymentGatewayService::class)->setGateway($gatewayStub);

        $id = 1;
        $url = $this->getRouter()->generate('app_user_feature_listing_action', [
            'id' => $id,
            'featuredPackage' => 1,
        ]);
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_feature'.$id);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode());
        $returnUrl = $client->getResponse()->headers->get('location');

        // notify from payment gateway
        self::ensureKernelShutdown();
        $client = static::createClient();
        $this->getTestContainer()->get(SettingsDto::class)->setPaymentGateway(PaymentGatewayEnum::PRZELEWY24);
        $gatewayStub->method('completePurchase')->willReturnCallback(function () {
            $responseStub = $this->createMock(RedirectResponseInterface::class);
            $responseStub->method('getData')->willReturn([
                'id' => 'TEST-PAYMENT_ID',
                'state' => 'success',
                'error' => '',
            ]);
            $responseStub->method('isSuccessful')->willReturn(true);
            $requestStub = $this->createMock(RequestInterface::class);
            $requestStub->method('send')->willReturn($responseStub);

            return $requestStub;
        });
        $this->getTestContainer()->get(Przelewy24PaymentGatewayService::class)->setGateway($gatewayStub);
        $client->request('POST', $this->getRouter()->generate('app_payment_gateway_notify', [
            'paymentAppToken' => $appPaymentToken,
        ]), [
            'p24_order_id' => '1111',
            'p24_amount' => '100',
        ]);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // go to return url
        self::ensureKernelShutdown();
        $client = static::createClient();
        $this->loginUser($client);
        $client->request('GET', $returnUrl);
        self::assertEquals('app_payment_await_confirmation', $client->getRequest()->get('_route'));

        // follow redirect after payment success
        $client->request('GET', $this->getRouter()->generate('app_payment_await_confirmation_redirect', [
            'paymentAppToken' => $appPaymentToken,
        ]));
        self::assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_feature_listing', $client->getRequest()->get('_route'));
    }
}
