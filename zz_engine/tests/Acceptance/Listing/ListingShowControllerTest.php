<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Listing;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class ListingShowControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        // check not expired
        $client->request('GET', $this->getRouter()->generate('app_listing_show', [
            'id' => 1,
            'slug' => 'test-listing-title',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringNotContainsString('Listing has expired', (string) $client->getResponse()->getContent());

        // set as expired
        $this->executeSql(/** @lang MySQL */ <<<'EOT'
            UPDATE listing SET valid_until_date = '2010-01-01 00:00:00' WHERE id = 1
EOT);
        $client->request('GET', $this->getRouter()->generate('app_listing_show', [
            'id' => 1,
            'slug' => 'test-listing-title',
        ]));
        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Listing has expired', (string) $client->getResponse()->getContent());
    }
}
