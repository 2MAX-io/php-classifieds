<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;

/**
 * @internal
 */
class IndexPageTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;

    public function testIndexPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', '/');

        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
