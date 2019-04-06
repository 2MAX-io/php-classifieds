<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class PoliceLogForUserMessageControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'listing' => 1,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('test message', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'user' => 1,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('test message', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'user' => -1,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'thread' => 1,
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('test message', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'thread' => -1, // should not find
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'query' => 'test message',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('test message', (string) $client->getResponse()->getContent());

        $client->request('GET', $this->getRouter()->generate('app_admin_police_log_user_message', [
            'query' => 'should find noting',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());
    }
}
