<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Tests\Base\AppIntegrationTest;
use App\Tests\Base\DatabaseTestHelper;
use App\Tests\Base\LoginTrait;
use Symfony\Component\Routing\RouterInterface;

/**
 * @internal
 * @coversNothing
 */
class AdminIndexPageTest extends AppIntegrationTest
{
    use LoginTrait;
    use DatabaseTestHelper;

    public function testIndexPage(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginAdmin($client);
        $url = $this->getRouter()->generate('app_admin_index');

        $client->request('GET', $url);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }

    protected function getRouter(): RouterInterface
    {
        return static::$kernel->getContainer()->get('router');
    }
}
