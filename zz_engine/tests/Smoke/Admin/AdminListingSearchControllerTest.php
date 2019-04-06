<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class AdminListingSearchControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_admin_listing_search'];
    }

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_listing_search', [
            'query' => 'test',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    public function testHidden(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_listing_search', [
            'publicDisplay' => '0',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }

    public function testMultipleConditions(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = '/admin/red5/listing/search?query=query&user=%40&category=1&publicDisplay=1&featured=1&adminActivated=1&adminRejected=1&adminRemoved=0&userDeactivated=1&userRemoved=0';
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertSame(200, $response->getStatusCode());
    }
}
