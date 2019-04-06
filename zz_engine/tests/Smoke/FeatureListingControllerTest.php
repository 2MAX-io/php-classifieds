<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestDataEnum;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @internal
 */
class FeatureListingControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return [
            'app_user_feature_listing_pay',
            'app_user_feature_listing_pay_by_balance_confirm',
        ];
    }

    public function testFeature(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);
        $this->executeSql("INSERT INTO payment (id, user_id, type, description, amount, gateway_amount_paid, currency, datetime, gateway_status, paid, delivered, canceled, gateway_payment_id, app_payment_token, gateway_name, gateway_mode) VALUES (2, 1, 'BALANCE_TOP_UP_TYPE', 'Payment for balance top up, amount: 10000', 1000000, 1000000, 'USD', '2021-04-01 06:15:24', 'approved', 1, 1, 0, 'PAYID-MBSWJ7A6N1568643W3977626', 'wieuo1tqreou0x2djn9ieyba21iov17xmerni5mc5ovqqolcpwn1brbybg6hiywq', 'paypal', 'sandbox');");
        $this->executeSql('INSERT INTO payment_for_balance_top_up (id, payment_id, user_id) VALUES (1, 2, 1);');
        $this->executeSql("INSERT INTO user_balance_change (id, user_id, payment_id, balance_change, balance_final, datetime, description) VALUES (1, 1, 2, 1000000, 1000000, '2021-04-01 06:32:55', 'Topping up the account balance');");
        $this->executeSql("UPDATE setting SET value = '1' WHERE name = 'paymentAllowed'");

        $id = 1;
        $url = $this->getRouter()->generate('app_user_feature_listing_pay', [
            'id' => $id,
            'package' => TestDataEnum::PAID_PACKAGE_ID,
        ]);
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_feature'.$id);
        $crawler = $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Do you confirm payment of: 1.00 USD, for package: feature package test, using account balance?', (string) $client->getResponse()->getContent());
        $confirmButton = $crawler->selectButton('Confirm');
        $client->submit($confirmButton->form());

        self::assertSame('app_user_feature_listing_pay_by_balance_confirm', $client->getRequest()->get('_route'));
        self::assertSame('/user/feature/1', $client->getResponse()->headers->get('location'));
    }

    public function testFeatureDemo(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_feature_listing', [
            'id' => 1,
        ]));
        $client->submitForm('Feature listing', [
            'feature[package]' => TestDataEnum::DEMO_PACKAGE_ID,
        ]);
        $client->followRedirect();
        self::assertSame(302, $client->getResponse()->getStatusCode());
        self::assertSame('/user/feature/1?demoStarted=1', $client->getResponse()->headers->get('location'));
        $client->followRedirect();
        self::assertStringContainsString('Listing has been featured as demonstration, to see how it looks click', (string) $client->getResponse()->getContent());
    }
}
