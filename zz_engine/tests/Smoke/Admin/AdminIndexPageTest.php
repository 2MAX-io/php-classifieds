<?php

declare(strict_types=1);

namespace App\Tests\Smoke\Admin;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class AdminIndexPageTest extends AppIntegrationTestCase
{
    use LoginTestTrait;
    use DatabaseTestTrait;
    use RouterTestTrait;

    public function testIndexPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $url = $this->getRouter()->generate('app_admin_index');

        $client->request('GET', $url);
        self::assertSame(200, $client->getResponse()->getStatusCode());
    }
}
