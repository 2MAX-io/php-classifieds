<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
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
            'app_user_feature_listing_as_demo',
            'app_user_feature_listing_action',
        ];
    }

    public function testFeatureAsDemo(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $id = 1;
        $csrfToken = $this->getTestContainer()->get(CsrfTokenManagerInterface::class)->getToken('csrf_featureAsDemo'.$id);
        $url = $this->getRouter()->generate('app_user_feature_listing_as_demo', [
            'id' => $id,
        ]);
        $client->request('PATCH', $url, [
            '_token' => $csrfToken->getValue(),
        ]);
        $response = $client->getResponse();

        self::assertEquals(302, $response->getStatusCode());
        self::assertEquals('/user/feature/1?demoStarted=1', $client->getResponse()->headers->get('location'));
    }

    public function testFeature(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);
        $this->executeSql("INSERT INTO payment (id, user_id, type, description, amount, gateway_amount_paid, currency, datetime, gateway_status, paid, delivered, canceled, gateway_payment_id, app_payment_token, gateway_name, gateway_mode) VALUES (2, 1, 'BALANCE_TOP_UP_TYPE', 'Payment for balance top up, amount: 10000', 1000000, 1000000, 'USD', '2021-04-01 06:15:24', 'approved', 1, 1, 0, 'PAYID-MBSWJ7A6N1568643W3977626', 'wieuo1tqreou0x2djn9ieyba21iov17xmerni5mc5ovqqolcpwn1brbybg6hiywq', 'paypal', 'sandbox');");
        $this->executeSql('INSERT INTO payment_for_balance_top_up (id, payment_id, user_id) VALUES (1, 2, 1);');
        $this->executeSql("INSERT INTO classifieds_test.user_balance_change (id, user_id, payment_id, balance_change, balance_final, datetime, description) VALUES (1, 1, 2, 1000000, 1000000, '2021-04-01 06:32:55', 'Topping up the account balance');");
        $this->executeSql("UPDATE setting SET value = '1' WHERE name = 'paymentAllowed'");

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

        self::assertEquals(302, $response->getStatusCode());
        self::assertEquals('/user/feature/1', $client->getResponse()->headers->get('location'));
    }
}
