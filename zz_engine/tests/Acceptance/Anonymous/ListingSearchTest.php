<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Anonymous;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ListingSearchTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', $this->getRouter()->generate('app_listing_search', [
            'query' => 'test',
        ]));

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
