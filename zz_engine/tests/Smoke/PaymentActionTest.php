<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Enum\ParamEnum;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 * @coversNothing
 */
class PaymentActionTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_payment_cancel',
            'app_payment_await_confirmation_redirect',
            'app_payment_status_refresh',
            'app_payment_await_confirmation',
        ];
    }

    public function testCancel(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $url = $this->getRouter()->generate('app_payment_cancel', [
            'paymentAppToken' => 'test_app_payment_token',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/user/feature/1', $client->getResponse()->headers->get('location'));
    }

    public function testRedirectAfterConfirmationAwait(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $url = $this->getRouter()->generate('app_payment_await_confirmation_redirect', [
            'paymentAppToken' => 'test_app_payment_token',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        self::assertEquals('/user/feature/1', $client->getResponse()->headers->get('location'));
    }

    public function testRefreshConfirmation(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $url = $this->getRouter()->generate('app_payment_status_refresh', [
            ParamEnum::PAYMENT_APP_TOKEN => 'test_app_payment_token',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }

    public function testAwaitConfirmation(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $url = $this->getRouter()->generate('app_payment_await_confirmation', [
            'paymentAppToken' => 'test_app_payment_token',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
