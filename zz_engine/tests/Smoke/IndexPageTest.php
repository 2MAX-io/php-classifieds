<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTest;
use App\Tests\Base\DatabaseTestHelper;

class IndexPageTest extends AppIntegrationTest
{
    use DatabaseTestHelper;

    public function testIndexPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();

        $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
