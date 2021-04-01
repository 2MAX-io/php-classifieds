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
 * @coversNothing
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

    public function testPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);

        $url = $this->getRouter()->generate('app_admin_listing_search', [
            'query' => 'test',
        ]);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode(), (string) $response->getContent());
    }
}
