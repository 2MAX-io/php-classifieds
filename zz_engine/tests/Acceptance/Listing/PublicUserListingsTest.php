<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Listing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class PublicUserListingsTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', $this->getRouter()->generate('app_public_listings_of_user', [
            'user' => 1,
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Listings of user', (string) $client->getResponse()->getContent());
    }
}
