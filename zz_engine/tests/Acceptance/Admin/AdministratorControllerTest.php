<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Enum\TestDataEnum;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class AdministratorControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_administrator_list'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(TestDataEnum::LOGIN_ADMIN, (string) $client->getResponse()->getContent());

        // should not find
        $client->request('GET', $this->getRouter()->generate('app_admin_administrator_list', [
            'query' => 'should not find any',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());

        // should find
        $client->request('GET', $this->getRouter()->generate('app_admin_administrator_list', [
            'query' => TestDataEnum::LOGIN_ADMIN,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString(TestDataEnum::LOGIN_ADMIN, (string) $client->getResponse()->getContent());
    }
}
