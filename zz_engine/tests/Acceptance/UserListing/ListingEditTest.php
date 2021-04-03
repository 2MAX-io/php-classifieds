<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\UserListing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ListingEditTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_listing_edit'];
    }

    public function testEditListing(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_listing_edit', [
            'id' => 1,
        ]));
        $client->submitForm('Update', [
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_user_listing_edit', $client->getRequest()->attributes->get('_route'));
    }
}
