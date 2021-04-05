<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\User;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class UserMyAccountControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_listing_list'));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Test listing title', (string) $client->getResponse()->getContent());

        // should not find
        $client->request('GET', $this->getRouter()->generate('app_user_listing_list', [
            'query' => 'should not find any',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('no records found', (string) $client->getResponse()->getContent());

        // should find
        $client->request('GET', $this->getRouter()->generate('app_user_listing_list', [
            'query' => 'Test listing title',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Test listing title', (string) $client->getResponse()->getContent());
    }
}
