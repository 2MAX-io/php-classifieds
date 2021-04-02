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
 * @coversNothing
 */
class ListingAddTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_user_listing_new'];
    }

    public function testAddListing(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_listing_new'));
        $client->submitForm('Save', [
            'listing[title]' => 'test listing',
            'listing[description]' => 'test listing',
            'listing[category]' => '140',
        ]);
        $response = $client->getResponse();
        self::assertEquals(302, $response->getStatusCode(), (string) $response->getContent());
        $client->followRedirect();
        self::assertSame('app_user_listing_edit', $client->getRequest()->attributes->get('_route'));
    }
}
