<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ChangeUserBalanceTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    public const NEW_LOGIN = 'test-new-administrator@example.com';
    public const NEW_PASSWORD = 'testnewpassword';

    public static function getRouteNames(): array
    {
        return [
            'app_admin_user_change_balance',
        ];
    }

    public function testChangeUserBalance(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        // submit form
        $client->request('GET', $this->getRouter()->generate('app_admin_user_change_balance', [
            'id' => 1,
        ]));
        $client->submitForm('Change balance', [
            'user_change_balance[newBalance]' => 10,
            'user_change_balance[changeReason]' => 'test change reason',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());

        // follow redirect after submit
        $client->followRedirect();
        self::assertSame('app_admin_user_change_balance', $client->getRequest()->attributes->get('_route'));
    }
}
